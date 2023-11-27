

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
        <div id="user">
            <img id="avatar-image" src="https://avatars.akamai.steamstatic.com/{{ Auth::user()->avatar_hash }}_medium.jpg" alt="profile image" />
            <div id="user-dropdown-wrapper">
                <div id="user-dropdown-arrow"></div>
                <div id="user-dropdown">
                    <a class="user-menu-item" href="{{ route('logout') }}">
                        <img class="user-menu-icon" src="icons/logout.svg" alt="Logout Icon">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}" id="login">
            <img src="images/sign-in-through-steam.png" alt="Login image">
        </a>
    @endauth
    

</header>