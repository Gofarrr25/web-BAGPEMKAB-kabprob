<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class OrganizationMember extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'position',
        'type',
        'image',
        'parent_id',
        'order_index',
        'is_active',
    ];

    public function parent()
    {
        return $this->belongsTo(OrganizationMember::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(OrganizationMember::class, 'parent_id')->orderBy('order_index');
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
