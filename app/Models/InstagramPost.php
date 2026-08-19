<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class InstagramPost extends Model
{
    use LogsActivity;

    protected $fillable = [
        'image',
        'caption',
        'post_url',
        'order_index',
        'is_active',
    ];

    /**
     * Dapatkan Embed URL Instagram otomatis dari post_url
     */
    public function getEmbedUrlAttribute()
    {
        if ($this->post_url && preg_match('/instagram\.com\/(?:p|reel|tv)\/([A-Za-z0-9_-]+)/i', $this->post_url, $matches)) {
            return 'https://www.instagram.com/p/' . $matches[1] . '/embed';
        }
        return null;
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
