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


    /**
     * Undocumented function
     *
     * @return array
     */
    public function getRankPercentageAttribute()
    {
        //Compte le nombre d'utilisateurs totaux
        $totalUsersCount = User::count();
        if ($totalUsersCount === 0) {
            return 100;
        }

        $user = auth()->user();

        //Compte le nombre de skins de l'utilisateur connecté dans le container
        $userSkinsCount = $user->skins()->where('container_id', $this->id)->count();
        
        //Récupère le nombre d'utilisateur qui ont  plus ou égal de skins
        //En gros si le user a 3 skins, et deux autres personnes ont 1 skin chacun, ça retournera 1
        $higherOrEqualUsersCount = User::join('skin_user', 'users.id', '=', 'skin_user.user_id')
        ->join('skins', 'skin_user.skin_id', '=', 'skins.id')
        ->join('containers', 'containers.id', '=', 'skins.container_id')
        ->select('users.id') // Specify the column you need
        ->where('containers.id', $this->id)
        ->groupBy('users.id')
        ->havingRaw('COUNT(DISTINCT skins.id) >= ?', [$userSkinsCount])
        ->count();

        //Intval pour ne  pas mettre les ,
        $rankPercentage = intval(($higherOrEqualUsersCount/$totalUsersCount)*100);

        //Si il n'y a personne avec qui comparer on met 100%
        if($higherOrEqualUsersCount === 0)
        {
            $rankPercentage = 100;
        }

        return $rankPercentage;
    }
}
