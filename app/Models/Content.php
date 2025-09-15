<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id', 'type', 'text', 'video_url', 'link', 'image_path', 'position', 'meta'
    ];

    protected $casts = ['meta' => 'array'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    // Get full image URL
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }
        return null;
    }

    // Check if content has specific type
    public function isType($type)
    {
        return $this->type === $type;
    }
}
