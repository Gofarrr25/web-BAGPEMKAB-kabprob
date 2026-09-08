<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = ['gallery_id', 'type', 'file_path', 'video_url', 'order_index'];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }
}
