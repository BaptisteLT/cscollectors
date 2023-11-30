<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Carbon\Carbon;
use App\Models\Skin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class InventoryRefreshController extends Controller
{
    private $http;

    public function __construct(Http $http)
    {
        $this->http = $http;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        //Retrieve user's inventory
        
        if (Auth::check()) {
            try{

                $user = auth()->user();

                $enoughPassHasPassed = $this->checkIfEnoughTimeHasPassed($user->lastAPIRequest, 300);
                //Si l'utilisateur n'a pas attendu son cooldown alors on lui dit d'attendre un peu
                if(!$enoughPassHasPassed)
                {
                    Session::flash('error', 'Inventory refresh is on cooldown.');
                    return redirect()->route('homepage');
                }

                /*//Si le nombre de requêtes dépasse les 4 dans les 1 dernière minute, on bloque pour éviter un ban IP de la part de steam*/
                $currentDateTime = now()->toDateTimeString();
                $oneMinutesAgo = now()->subMinutes(1)->toDateTimeString();
                $numberOfUsers1 = DB::table('users')
                    ->join('skin_user', 'users.id', '=', 'skin_user.user_id')
                    ->whereBetween('skin_user.updated_at', [$oneMinutesAgo, $currentDateTime])
                    ->distinct('users.id')
                    ->count();
                /*//Si le nombre de requêtes dépasse les 15 dans les 5 dernières minutes, on bloque pour éviter un ban IP de la part de steam*/
                $fiveMinutesAgo = now()->subMinutes(5)->toDateTimeString();
                $numberOfUsers5 = DB::table('users')
                    ->join('skin_user', 'users.id', '=', 'skin_user.user_id')
                    ->whereBetween('skin_user.updated_at', [$fiveMinutesAgo, $currentDateTime])
                    ->distinct('users.id')
                    ->count();
                if($numberOfUsers5 >= 12 || $numberOfUsers1 >=3)
                {
                    Session::flash('error', 'The server has received too many requests. Please try again later.');
                    return redirect()->route('homepage');
                }
      
                // Detach all skins from the user (on écrase les associations skin_user)
                $user->skins()->detach();

                $steam64Id = $user->steam64_id;
    
                //On  récupère les skins
                $response = $this->http::get("https://steamcommunity.com/inventory/$steam64Id/730/2?l=english&count=1000");

                //On enregistre la date de la dernière requête
                $user->lastAPIRequest = Carbon::now();
                $user->save();

                if($response->status() === 403)
                {
                    Session::flash('error', 'Your steam inventory is private. Please set it to public.');
                    return redirect()->route('homepage');
                }

                if($response->status() === 429)
                {
                    Session::flash('error', 'The server has reached the limit for Steam inventory requests. Please retry after a minute.');
                    return redirect()->route('homepage');
                }
                $data = json_decode($response->body());
    
                $skinsToAssociate = [];

            
                foreach($data->descriptions as $item)
                {
                    //On enlève le souvenir devant sinon il n'arrive pas à faire la correspondance
                    $item->name = str_replace('Souvenir ', '', $item->name);

                    //On regarde si un item existe en base de données
                    $skin = Skin::where('name', $item->name)->first();
                    //Et donc s'il existe on associe l'utilisateur au skin, et on vérifie aussi qu'il n'est pas un doublon
                    if($skin !==null && !isset($skinsToAssociate[$item->name]))
                    {
                        $skinsToAssociate[$item->name] = $skin;
                    }
                }
                //On sauvegarde tout à la fin en une seule fois
                $user->skins()->saveMany($skinsToAssociate);

                Session::flash('success', 'The inventory has been loaded successfully.');
            }
            catch(Exception $e)
            {
                Session::flash('error', 'An error occured while loading your inventory, please try again later.');
            }
        }
        else
        {
            Session::flash('error', 'Please log in to refresh your inventory.');
        }
        
        return redirect()->route('homepage');
    }

    /**
     * Regarde si assez de temps a passé pour refaire une requête à Steam
     *
     * @return void
     */
    private function checkIfEnoughTimeHasPassed($date, $seconds)
    {
        $currentTime = new DateTime();
        $specifiedTime = new DateTime($date);

        $interval = $currentTime->diff($specifiedTime);

        // Check if the difference is greater than 5 minutes (300 seconds)
        if (empty($date) || $interval->i >= $seconds/60) {
            return true;
        } else {
            return false;
        }
    }
}
