<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Gallery extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'file_path',
        'video_url',
        'is_active',
        'created_at',
    ];

    public function galleryItems()
    {
        return $this->hasMany(GalleryItem::class)->orderBy('order_index');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getYoutubeIdAttribute()
    {
        $videoUrl = $this->video_url;
        
        // If the main video_url is null (e.g. video album), get the first item's video_url
        if (!$videoUrl && $this->type === 'video') {
            $firstItem = $this->galleryItems()->whereNotNull('video_url')->orderBy('order_index')->first();
            if ($firstItem) {
                $videoUrl = $firstItem->video_url;
            }
        }
        
        if (!$videoUrl) return null;

        $url = trim($videoUrl);

        // Comprehensive regex: matches ALL known YouTube URL formats
        // - youtube.com/watch?v=ID
        // - youtube.com/embed/ID
        // - youtube.com/v/ID
        // - youtube.com/shorts/ID
        // - youtube.com/live/ID
        // - youtu.be/ID
        // - m.youtube.com/watch?v=ID
        // - youtube-nocookie.com/embed/ID
        // - With or without query params (?si=, &list=, &index=, etc.)
        $pattern = '/(?:(?:https?:)?\/\/)?'           // Optional protocol
            . '(?:(?:www|m)\.)?'                       // Optional www. or m.
            . '(?:youtube(?:-nocookie)?\.com|youtu\.be)' // Domain
            . '(?:\/(?:[\w\-]+\?v=|embed\/|shorts\/|live\/|v\/))'  // Path variants
            . '?([a-zA-Z0-9_\-]{11})'                 // Video ID (11 chars)
            . '(?:[?&].*)?$/';                         // Optional query string

        // Try watch?v= parameter first (most common)
        if (preg_match('/[?&]v=([a-zA-Z0-9_\-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        // Try path-based formats: /embed/ID, /shorts/ID, /live/ID, /v/ID
        if (preg_match('/(?:youtube(?:-nocookie)?\.com|youtu\.be)\/(?:embed|shorts|live|v)\/([a-zA-Z0-9_\-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        // Try youtu.be/ID short format
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_\-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        // Fallback: if it's exactly 11 characters (raw video ID)
        if (preg_match('/^[a-zA-Z0-9_\-]{11}$/', $url)) {
            return $url;
        }

        return null;
    }

    public function getEmbedUrlAttribute()
    {
        $id = $this->youtube_id;
        // Embed URL tanpa autoplay — autoplay ditangani oleh YouTube IFrame API di frontend
        return $id ? "https://www.youtube.com/embed/{$id}?rel=0&enablejsapi=1&origin=" . url('/') : null;
    }

    public function getWatchUrlAttribute()
    {
        $id = $this->youtube_id;
        return $id ? "https://www.youtube.com/watch?v={$id}" : null;
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->type === 'image' && $this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        
        $id = $this->youtube_id;
        return $id ? "https://img.youtube.com/vi/{$id}/hqdefault.jpg" : asset('images/default-video.jpg');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Gallery has been {$eventName}");
    }
}
