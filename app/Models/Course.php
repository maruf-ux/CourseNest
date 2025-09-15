<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
   use HasFactory;

    protected $fillable = ['title', 'description', 'category', 'meta'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('position');
    }

    // Get total content count across all modules
    public function getTotalContentsAttribute()
    {
        return $this->modules->sum(function ($module) {
            return $module->contents->count();
        });
    }
}
