<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Document extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'document_date',
        'file_path',
        'zip_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(function (string $eventName) {
                if ($eventName === 'created') return 'Tambah Data — Dokumen PPID';
                if ($eventName === 'updated') return 'Edit Data — Dokumen PPID';
                if ($eventName === 'deleted') return 'Hapus Data — Dokumen PPID';
                return "Aktivitas Dokumen PPID: {$eventName}";
            });
    }
}
