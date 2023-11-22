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

        //Récupère le contenu de la caisse dont on a passé l'url en paramètre correspondant à l'URL sur le steam market
        $skins = $this->csContainersService->retrieveContainerItems('https://steamcommunity.com/market/listings/730/Fracture%20Case');

        dump($skins);die;


        return view('welcome');


        /*
        crawl les infos de la caisse

        require_once 'vendor/autoload.php'; // Assuming you have the necessary autoload file

        use Symfony\Component\DomCrawler\Crawler;

        // Your HTML content (replace this with your actual HTML content)
        $htmlContent = '
            ... (your HTML content here) ...
        ';

        // Use Symfony DomCrawler to parse HTML
        $crawler = new Crawler($htmlContent);

        // Find the script tag containing the g_rgAssets data
        $scriptTag = $crawler->filter('script:contains("var g_rgAssets =")')->first();

        // Extract the JavaScript code containing g_rgAssets
        $javascriptCode = $scriptTag->text();

        // Extract the g_rgAssets object as JSON
        preg_match('/var g_rgAssets = ({.*?});/', $javascriptCode, $matches);
        $g_rgAssetsJson = $matches[1] ?? null;

        // Decode the JSON in PHP
        $g_rgAssets = json_decode($g_rgAssetsJson, true);

        // Access the specific data you're interested in
        $itemData = $g_rgAssets['730']['2'] ?? null;

        // Print the retrieved item data
        print_r($itemData);
        */
        
    }
}
