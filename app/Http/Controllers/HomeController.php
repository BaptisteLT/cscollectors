<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Skin;
use App\Models\Container;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Service\CSContainersService;

class HomeController extends Controller
{
    protected $csContainersService;

    public function __construct(CSContainersService $csContainersService)
    {
        $this->csContainersService = $csContainersService;
    }

    public function home(Request $request): View
    {
        //http://cscollectors.com/
        //TODO:  faire un système de trophées bronze, argent, or

        //TODO: filter bronze, silver, gold
        //TODO: pagination


        //TODO: Bug sur petit écran


        /*
        try{
            $containers = $this->csContainersService->getAllContainers();

            $count = 0;
            foreach($containers['data'] as $containerData)
            {
                $originalName = $containerData['hash_name'];
                //Pour les conteneurs souvenir, on enlève tout  ce qui n'est pas utile afin d'éviter une duplication de packages souvenir pour toutes les années
                if(str_contains(strtolower($containerData['hash_name']),'souvenir'))
                {
                    // Define a regular expression pattern to match the year (any four digits)
                    $pattern = '/.*\b(\d{4})\b/';

                    //On supprime la date et tout se qui se trouve avant la date comme pour Atlanta 2017 Cache Souvenir Package
                    //On aura donc Cache Souvenir Package
                    $containerData['hash_name'] = preg_replace($pattern, '', $originalName);
                    //Et ici on se débarasse de "Souvenir Package"
                    $containerData['hash_name'] = str_replace('Souvenir Package', '', $containerData['hash_name']);
                }

                //TODO  rechercher  que packages et cases
                $dbContainer = Container::where('name', $containerData['hash_name'])->first();

                //dump($containerData['hash_name']);
                //dump(($dbContainer === null && (str_contains($containerData['hash_name'], 'case') || str_contains($containerData['hash_name'], 'package'))));

                //Si le conteneur n'existe en base de données on le crée
                if(($dbContainer === null && (str_contains(strtolower($originalName), 'case') || str_contains(strtolower($originalName), 'package'))))
                {
                    (str_contains(strtolower($originalName), 'case') || str_contains(strtolower($originalName), 'package'));
                    //dump($originalName);
                    $count = $count + 1;
                    $skinsData = $this->csContainersService->retrieveContainerItems('https://steamcommunity.com/market/listings/730/'.$originalName);

                    //Pour les collections souvenir, on regarde si le premier skin n'est pas déjà en base de données
                    if($skinsData!==null && $skinsData['error'] === null)
                    {
                        $skins = [];
                        DB::beginTransaction();
                        $container = new Container([
                            'name'=>$containerData['hash_name']
                        ]);
                        $container->save();

                        //On fait un groupe de tous les skins qu'on persist en une fois
                        foreach($skinsData['data'] as $skinData)
                        {
                            $skins[] = [
                                'name' => $skinData['value'],
                                'color' => $skinData['color'],
                                'container_id' => $container->id,
                            ];
                        }
                        Skin::insert($skins);
                        DB::commit();
                    }
                    sleep(10);
                }
            }
        }
        catch(\Exception $e)
        {
            dump($e);die;
            //TODO: something with it
            echo $e->getMessage();
        }*/
        
        //TODO: do some cahing here and delete it on inventory refresh

        //Récupère les conteneurs et tri les skins par ordre ASC de rareté
        $containers = Container::with([
            'skins' => function ($query) {
                $query->select('skins.*', 'rarities.color', 'rarities.background_color')
                    ->join('rarities', 'skins.rarity_id', '=', 'rarities.id')
                    ->orderBy('rarities.rarity', 'asc');
            },
            'skins.users' => function ($query) {
                $query->where('user_id', auth()->id());
            },
        ])
        //Récupère le nombre de skins qu'à l'utilisateur dans la collection
        ->withCount(['skins', 'skins as user_total_skins' => function ($query) {
            $query->whereHas('users', function ($subquery) {
                $subquery->where('user_id', auth()->id());
            });
        }])
        //Recherche par nom de container
        ->when($request->has('container_name'), function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->input('container_name') . '%');
        })
        //Utilisé pour ne retrouver que les collections où l'utilisateur a au moins 1 skin
        /*->whereHas('skins', function ($query) {
            $query->whereHas('users', function ($subquery) {
                $subquery->where('user_id', auth()->id());
            });
        })*/
        //On tri par le ratio, c'est a dire les collections les plus complétées au moins complétées et inversement
        ->orderByDesc(DB::raw('user_total_skins / nullif(skins_count, 0)')) //nullif Pour éviter une division par 0
        ->get();

        //TODO: faire le filtre ratio ASC DESC


        $user = auth()->user();

        $interval=0;
        if($user)
        {
            /*Calcul du cooldown restant en secondes*/
            $currentTime = new DateTime();
            $specifiedTime = new DateTime($user->lastAPIRequest);
            $interval = $currentTime->diff($specifiedTime);
            $interval = $interval->s + $interval->i*60;
            //Cooldown restant en secondes
            $interval = 300 - $interval;
        }

        
        return view('welcome', [
            'containers' => $containers,
            'container_name_query' => $request->input('container_name'),
            'cooldown' => $interval >= 1 ? $interval : null
        ]); 








        /*
SELECT
    containers.id,
    COUNT(skins.id) AS countTotalSkins,
    COUNT(CASE WHEN skin_user.user_id = 1 THEN skins.id END) AS countUserTotalSkins,
    COUNT(CASE WHEN skin_user.user_id = 1 THEN skins.id END) / NULLIF(COUNT(skins.id), 0) AS ratio
FROM
    containers
    LEFT JOIN skins ON containers.id = skins.container_id
    LEFT JOIN skin_user ON skins.id = skin_user.skin_id AND skin_user.user_id = 1
WHERE
    containers.id = 1
GROUP BY
    containers.id;

        */
    }
}
