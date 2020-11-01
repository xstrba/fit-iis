<div id="topbar">
    <button class="navbar-toggler sidebar-collapse-button"
            type="button"
            data-toggle="collapse"
            data-target="#sidebar-wrapper"
            aria-controls="sidebar-wrapper"
            aria-expanded="true"
            aria-label="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>
    @auth
        <span class="ml-5">{{ $auth->nickname }} - {{ __('roles.' . $auth->role) }}</span>
    @endauth
</div>
