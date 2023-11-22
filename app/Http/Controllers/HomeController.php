<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Service\CSContainersService;

class HomeController extends Controller
{
    protected $csContainersService;

    public function __construct(CSContainersService $csContainersService)
    {
        $this->csContainersService = $csContainersService;
    }

    public function home(): View
    {
        //http://cscollectors.com/
        //TODO: idée de génie, mettre un classement de ceux qui ont le plus d'items par collection

        //TODO: mettre tout ça dans un CRON job qui va rechercher les nouvelles caisses une fois par jour en regardant si elle existe en BDD
        //TODO: boucler sur toutes les caisses du marché
        //TODO: mettre en BDD avec une table Container qui est reliée à 1 ou plusieurs Item



        //Récupère le contenu de la caisse dont on a passé l'url en paramètre correspondant à l'URL sur le steam market                 //TODO: Il va falloir utiliser le hash_name qui est en anglais et rajouter les %20
        $skins = $this->csContainersService->retrieveContainerItems('https://steamcommunity.com/market/listings/730/ESL%20One%20Cologne%202014%20Mirage%20Souvenir%20Package');

        dump($skins);die;


        return view('welcome');
    }
}
