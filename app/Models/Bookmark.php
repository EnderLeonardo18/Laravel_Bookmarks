<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    protected $fillable = [
        'title',
        'url',
        'alternative_urls',
        'description',
        'image_preview',
        'category',
        'user_id',
        'status',
        'has_new_episode',
        'progress_note',
        'progress_url',
        'order'

    ];

    // 🔴 ESTO ES ESENCIAL: Convierte el JSON de la BD automáticamente a un Array de PHP
    protected $casts = [
        'alternative_urls' => 'array',
        'has_new_episode' => 'boolean',
    ];

    // Relación inversa: Un marcador pertenece a un usuario
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }


    // Metodo donde ordena las card en el Frontend, y que se puede mover y mantener ese orden
    protected static function booted(){
        static::creating(function ($bookmark){
            if(is_null($bookmark->order)) {
                $maxOrder = static::where('user_id', $bookmark->user_id)->max('order');
                $bookmark->order = $maxOrder + 1;
            }
        });
    }
}
