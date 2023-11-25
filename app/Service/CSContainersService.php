<?php
namespace App\Service;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class CSContainersService
{
    private $http;

    private $log;

    public function __construct(Http $http, Log $log)
    {
        $this->http = $http;
        $this->log = $log;
    }

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
            $response = $this->http::get(str_replace(' ', '%20',$url));

            
            //Retourne un message d'erreur si on a dépassé le nombre de requêtes limites de l'API
            if($response->status() === 429)
            {
                return ['data' => null, 'containerName' => null, 'error' => 'Error 429: Too Many Requests'];
            }
            else if($response->status() !== 200)
            {
                return ['data' => null, 'containerName' => null, 'error' => 'Error while fetching steam market'];
            }
            

            // Access the response body as a string
            $htmlContent = $response->body();

            $crawler = new Crawler;
            // Use Symfony DomCrawler to parse HTML
            $crawler->add($htmlContent);

            // Find the script tag containing the g_rgAssets data
            $scriptTag = $crawler->filter('script:contains("var g_rgAssets =")')->first();

            // Extract the JavaScript code containing g_rgAssets
            $javascriptCode = $scriptTag->text();

            
            $position = strpos($javascriptCode, '; var g_rgListingInfo = {');

            //On supprime tout se qui se trouve après "; var g_rgListingInfo = {", soit les infos inutiles
            if ($position !== false) {
                // Extract the portion of the string before "; var g_rgList"
                $modifiedString = substr($javascriptCode, 0, $position);
                //On supprime "var g_rgAppContextData =" qui est devant
                $modifiedString = str_replace('var g_rgAppContextData = ', '', $modifiedString);
            } else {
                //dump($url);
                //dump('1');
                //dump($htmlContent);die;
                return ['data' => null, 'containerName' => null, 'error' => 'Error while parsing the container\'s information'];
            }

            //Supprime tout ce qui est derrière 'var g_rgAssets = '
            $textToFind = 'var g_rgAssets = ';
            $position = strpos($modifiedString, $textToFind);

            if ($position !== false) {
                // Supprimer 'var g_rgAssets = ' et tout ce qui se trouve avant pour ne garder que le json
                $modifiedString = substr($modifiedString, $position + strlen($textToFind));
                 //On supprime aussi ce truc là que j'ai retrouvé dans un conteneur souvenir
                $modifiedString = str_replace('; var g_rgCurrency = []','',$modifiedString);
        
            } else {
                //dump($modifiedString);die;
                return ['data' => null, 'containerName' => null, 'error' => 'Error while parsing the container\'s information'];
            }

            // Decode the JSON in a PHP array
            $array = json_decode($modifiedString, true);

            if($array === null)
            {
                return ['data' => null, 'containerName' => null, 'error' => 'JSON is invalid and cannot be decoded'];
            }

            //Les informations de la caisse, on utilise reset car il y a une clé aléatoire que je ne peux pas déterminer à l'avance
            $caseData = reset($array[730][2]);

            $containerName = $caseData['name'];

            //Récupère les descriptions qui correspondent en partie aux items contenus dans la caisse
            $descriptions = $caseData['descriptions'];

            //On va boucler sur chaque description pour supprimer ceux qui ne sont pas des skins
            foreach($descriptions as $key => $item)
            {
                if(!(str_contains($item['value'], '|') || str_contains($item['value'], '(') || isset($item['color'])) 
                || empty($item['value'])
                || str_contains($item['value'], 'Container Series')
                || str_contains($item['value'], 'commemorates')
                || str_contains($item['value'], 'dropped during')
                || str_contains($item['value'], 'Contains one'))
                {
                    unset($descriptions[$key]);
                }
            }
            //Retourne les items contenus dans la caisse ou container
            return ['data' => $descriptions, 'containerName' => $containerName, 'error' => null];
    
        } catch (\Exception $e) {
            //dump($e);die;
            $this->log::error('An unexpected error occured: ' . $e->getMessage());
            return ['data' => null, 'containerName' => null, 'error' => 'An unexpected error occured'];
        }
    }

    public function getAllContainers()
    {
        try {
            $start = 0;
            $count = 100;
            $iterate = true;
            $containers = [];
            $error = null;

            //Boucle sur toutes les pages
            while($iterate === true)
            {
                // On fait un call au steam market  pour récup  tous  les containers
                $response = $this->http::get("https://steamcommunity.com/market/search/render?norender=1&start=$start&count=$count&q=&category_730_ItemSet%5B%5D=any&category_730_ProPlayer%5B%5D=any&category_730_StickerCapsule%5B%5D=any&category_730_TournamentTeam%5B%5D=any&category_730_Weapon%5B%5D=any&category_730_Type%5B%5D=tag_CSGO_Type_WeaponCase&appid=730");

                //Retourne un message d'erreur si on a dépassé le nombre de requêtes limites de l'API
                if($response->status() === 429)
                {
                    throw new Exception('Error 429: Too Many Requests');
                }
                else if($response->status() !== 200)
                {
                    throw new Exception('Error while fetching steam market');
                }

                $data = json_decode($response, true);

                //On construit l'array qui contiendra tous les containers
                foreach($data['results'] as $container)
                {
                    $containers[] = $container;
                }

                $start = $start + $count;

                //Permet de déterminer si on a bouclé sur toutes les pages afin de ne plus itérer.
                if($start > $data['total_count'])
                {
                    $iterate = false;
                }
            }
            
            return ['data' => $containers];

        } catch (\Exception $e) {
            $this->log::error('An unexpected error occured: ' . $e->getMessage());
            throw new Exception('An unexpected error occured' . $e->getMessage());
        }
    }
}