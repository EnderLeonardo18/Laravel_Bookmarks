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
        'user_id'
    ];

    // Relación inversa: Un marcador pertenece a un usuario
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
