<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rarity extends Model
{
    use HasFactory;

    protected $fillable = [
        'rarity',
        'color',
        'background_color'
    ];

    public function skins(): HasMany
    {
        return $this->hasMany(Skin::class);
    }
}
