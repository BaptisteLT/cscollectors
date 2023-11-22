<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CSCollection</title>
    </head>

    <!-- https://steamcommunity.com/inventory/76561198155379476/730/2?l=english -->
    
    <!-- Récupérer toutes les caisses (pagination à faire) https://steamcommunity.com/market/search/render?norender=1&start=0&count=100&q=&category_730_ItemSet%5B%5D=any&category_730_ProPlayer%5B%5D=any&category_730_StickerCapsule%5B%5D=any&category_730_TournamentTeam%5B%5D=any&category_730_Weapon%5B%5D=any&category_730_Type%5B%5D=tag_CSGO_Type_WeaponCase&appid=730 -->
    
    <!-- Récupérer le contenu de la  boite: https://steamcommunity.com/market/listings/730/Fracture%20Case -->

    <body>
        <div>
            @auth
                loggedIn
            @else
                notLoggedIn
            @endauth
        </div>
    </body>
</html>
