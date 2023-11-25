<?php

namespace App\Models;

use App\Models\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Skin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'color'
    ];

    public function container()
    {
        return $this->belongsTo(Container::class);
    }
}
