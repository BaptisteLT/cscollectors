

<header>
    <span>CSCollectors</span>

    <div id="search-container">
        <input id="search-input" type="text" placeholder="Search for a specific case or collection">
        <div id="search-icons">
            <img class="icon" src="icons/search-loop.svg" alt="Search loop icon">
            <img id="search-cross-icon" class="icon" src="icons/cross.svg" alt="Cross Icon">
        </div>
    </div>

    @if(auth()->check())
        <img id="avatar-image" src="https://avatars.akamai.steamstatic.com/{{ Auth::user()->avatar_hash }}_medium.jpg" alt="profile image" />
    @else
        notLoggedIn
    @endauth
    

</header>