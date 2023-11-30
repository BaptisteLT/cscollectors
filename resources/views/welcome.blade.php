<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts/general')
        <link rel="stylesheet" href="css/homepage.css">
        <script type="text/javascript" src="js/homepage.js"></script>
        <title>CSCollectors</title>
    </head>

    <!-- https://steamcommunity.com/inventory/76561198155379476/730/2?l=english -->
    
    <!-- Récupérer toutes les caisses (pagination à faire) https://steamcommunity.com/market/search/render?norender=1&start=0&count=100&q=&category_730_ItemSet%5B%5D=any&category_730_ProPlayer%5B%5D=any&category_730_StickerCapsule%5B%5D=any&category_730_TournamentTeam%5B%5D=any&category_730_Weapon%5B%5D=any&category_730_Type%5B%5D=tag_CSGO_Type_WeaponCase&appid=730 -->
    
    <!-- Récupérer le contenu de la  boite: https://steamcommunity.com/market/listings/730/Fracture%20Case -->

    <body>

        TODO inventory loading animation
        TODO mettre steam trade URL pour que ce soit plus rapide

        @include('layouts/header')

        @if(!auth()->check())
            <div id="overlay">
                <span>Please sign in through Steam to access your inventory.</span>
            </div>
        @endif

        <div class="container {{ !auth()->check() ? 'blurred' : '' }}">

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(auth()->check())
                <a id="refresh-inventory" href="/refresh-inventory">Refresh inventory
                    @if($cooldown)
                        in <span id="cooldown">{{ $cooldown }}</span>s
                    @endif
                </a>
            @endif

            @foreach ($containers as $container)
                <div class="cs-container">
                    <div class="cs-container-left">
                        <div class="cs-container-left-top">
                            <span class="container_name">{{ $container->name }}</span>
                            <img class="container-img" src="images/containers/{{ $container->image }}" alt="container image"/>
                            <span class="top_percentage" title="Based on the data of website users">
                                @if(auth()->check())
                                    Rank: {{ $container->rankPercentage }}% <span>({{ $container->user_total_skins }}/{{ $container->skins_count }})</span></span>
                                @endif
                        </div>
                    </div>

                    <div class="cs-container-right">
                        @foreach ($container->skins as $skin)
                            
                            <div class="skin-box-wrapper">
                                <div style="border: 1px solid #{{ $skin->color }}; background: #{{ $skin->user_has_skin ? $skin->background_color : '191D32' }};" 
                                class="skin-box {{ !$skin->user_has_skin ? 'blur' : '' }}">
                                    <img src="{{ $skin->image }}" alt="skin image"/>
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
