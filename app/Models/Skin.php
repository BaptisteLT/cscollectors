<?php

namespace App\Models;

use App\Models\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'color',
        'rarity_id'
    ];

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function rarity(): BelongsTo
    {
        return $this->belongsTo(Rarity::class);
    }

    // Add an accessor for user_has_skin attribute
    public function getUserHasSkinAttribute()
    {
        // Assuming there's a relationship between User and Skin
        return optional($this->users)->contains(auth()->id());
    }
    
}
