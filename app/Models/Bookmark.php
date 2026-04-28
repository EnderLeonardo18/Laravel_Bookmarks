<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    protected $fillable = [
        'title',
        'url',
        'description',
        'image_preview',
        'category',
        'user_id',
        'status',
        'progress_note',
        'progress_url',
        'order'

    ];

    // Relación inversa: Un marcador pertenece a un usuario
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }


    protected static function booted(){
        static::creating(function ($bookmark){
            if(is_null($bookmark->order)) {
                $maxOrder = static::where('user_id', $bookmark->user_id)->max('order');
                $bookmark->order = $maxOrder + 1;
            }
        });
    }
}
