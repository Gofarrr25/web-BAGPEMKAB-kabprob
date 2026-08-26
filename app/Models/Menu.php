<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $appends = ['link_url'];

    protected $fillable = [
        'title',
        'icon',
        'parent_id',
        'url',
        'page_id',
        'order_index',
        'target',
        'position',
        'is_active',
    ];

    protected static function booted()
    {
        static::saved(function ($menu) {
            \Illuminate\Support\Facades\Cache::forget('header_nav_menus');
            \Illuminate\Support\Facades\Cache::forget('footer_nav_menus');
        });

        static::deleted(function ($menu) {
            \Illuminate\Support\Facades\Cache::forget('header_nav_menus');
            \Illuminate\Support\Facades\Cache::forget('footer_nav_menus');
        });
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order_index');
    }

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function getLinkUrlAttribute()
    {
        // Special case: if this menu is a child of the "Dokumen" menu, force the URL to the dokumen route
        if ($this->parent && strtoupper($this->parent->title) === 'DOKUMEN') {
            return '/dokumen/' . str_replace(' ', '-', strtolower($this->title));
        }

        // If it has a specific non-page URL set (e.g. /struktur-organisasi), prioritize it
        if (!empty($this->url) && !str_starts_with($this->url, '/page/')) {
            return $this->url;
        }
        
        if ($this->page) {
            return '/page/' . $this->page->slug;
        }
        
        return $this->url ?? '#';
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
