<?php
namespace App\Service;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class CSContainersService
{
    /**
     * Retourne une erreur s'il y a, ou la liste des items contenus dans le container
     *
     * @param string $url
     * @return array
     */
    public function retrieveContainerItems($url): array
    {
        try {
            // Make a GET request using the Http facade
            $response = Http::get($url);

            //Retourne un message d'erreur si on a dépassé le nombre de requêtes limites de l'API
            if($response->status() !== 200)
            {
                return ['data' => null, 'error' => 'Error '.$response->status(). ': '.$response->body()];
            }

            // Access the response body as a string
            $htmlContent = $response->body();

            // Use Symfony DomCrawler to parse HTML
            $crawler = new Crawler($htmlContent);

            // Find the script tag containing the g_rgAssets data
            $scriptTag = $crawler->filter('script:contains("var g_rgAssets =")')->first();

            // Extract the JavaScript code containing g_rgAssets
            $javascriptCode = $scriptTag->text();

            
            $position = strpos( $javascriptCode, '; var g_rgListingInfo = {');

            //On supprime tout se qui se trouve après "; var g_rgListingInfo = {", soit les infos inutiles
            if ($position !== false) {
                // Extract the portion of the string before "; var g_rgList"
                $modifiedString = substr($javascriptCode, 0, $position);
                //On supprime "var g_rgAppContextData =" qui est devant
                $modifiedString = str_replace('var g_rgAppContextData = ', '', $modifiedString);
            } else {
                return ['data' => null, 'error' => 'Error while parsing the container\'s information'];
            }

            //Supprime tout ce qui est derrière 'var g_rgAssets = '
            $textToFind = 'var g_rgAssets = ';
            $position = strpos($modifiedString, $textToFind);
            if ($position !== false) {
                // Supprimer 'var g_rgAssets = ' et tout ce qui se trouve avant pour ne garder que le json
                $modifiedString = substr($modifiedString, $position + strlen($textToFind));
            } else {
                return ['data' => null, 'error' => 'Error while parsing the container\'s information'];
            }

            // Decode the JSON in PHP
            $array = json_decode($modifiedString, true);

            if($array === null)
            {
                return ['data' => null, 'error' => 'JSON is invalid and cannot be decoded'];
            }

            //Les informations de la caisse, on utilise reset car il y a une clé aléatoire que je ne peux pas déterminer à l'avance
            $caseData = reset($array[730][2]);
            //Récupère les descriptions qui correspondent en partie aux items contenus dans la caisse
            $descriptions = $caseData['descriptions'];

            //On va boucler sur chaque description pour supprimer ceux qui ne sont pas des skins
            foreach($descriptions as $key => $item)
            {
                if(!str_contains($item['value'], '|'))
                {
                    unset($descriptions[$key]);
                }
            }
            //Retourne les items contenus dans la caisse ou container
            return ['data' => $descriptions, 'error' => null];
    
        } catch (\Exception $e) {
            Log::error('An unexpected error occured: ' . $e->getMessage());
            return ['data' => null, 'error' => 'An unexpected error occured'];
        }
    }
}