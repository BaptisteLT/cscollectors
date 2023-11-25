<?php

namespace App\Models;

use App\Models\Skin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Container extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function skins()
    {
        return $this->hasMany(Skin::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
