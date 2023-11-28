<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts/general')
        <link rel="stylesheet" href="css/homepage.css">
        <title>CSCollectors</title>
    </head>

    <!-- https://steamcommunity.com/inventory/76561198155379476/730/2?l=english -->
    
    <!-- Récupérer toutes les caisses (pagination à faire) https://steamcommunity.com/market/search/render?norender=1&start=0&count=100&q=&category_730_ItemSet%5B%5D=any&category_730_ProPlayer%5B%5D=any&category_730_StickerCapsule%5B%5D=any&category_730_TournamentTeam%5B%5D=any&category_730_Weapon%5B%5D=any&category_730_Type%5B%5D=tag_CSGO_Type_WeaponCase&appid=730 -->
    
    <!-- Récupérer le contenu de la  boite: https://steamcommunity.com/market/listings/730/Fracture%20Case -->

    <body>
        @include('layouts/header')

        <div class="container">

            @foreach ($containers as $container)
                <div class="cs-container">
                    <div class="cs-container-left">
                        <div class="cs-container-left-top">
                            <span class="container_name">{{ $container->name }}</span>
                            <img class="container-img" src="images/containers/{{ $container->image }}" alt="container image"/>
                            <span class="top_percentage" title="Based on the data of website users">Rank: {{ $container->rankPercentage }}% <span>({{ $container->user_total_skins }}/{{ $container->skins_count }})</span></span>
                        </div>
                    </div>

                    <div class="cs-container-right">
                        @foreach ($container->skins as $skin)
                            
                            <div class="skin-box-wrapper">
                                <div style="border: 1px solid #{{ $skin->color }}; background: #{{ $skin->user_has_skin ? $skin->background_color : '191D32' }};" 
                                class="skin-box {{ !$skin->user_has_skin ? 'blur' : '' }}">
                                    <img src="https://raw.githubusercontent.com/ByMykel/CSGO-API/4fdf048a2b6c21494df4fe915f5fdea5accc6a61/public/images/econ/default_generated/weapon_deagle_cu_deagle_kitch_light.png" alt="skin image"/>
                                </div>
                                <span class="tooltip_item_name">{{ $skin->name }}</span>
                            </div>
                            
                            
                            
                            

                        @endforeach
                        <!-- TODO nullable false pour rarity -->
                    </div>
                </div>
            @endforeach

        </div>
    </body>
</html>
