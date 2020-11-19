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
        <span class="ml-4">{{ $auth->nickname }} - {{ __('roles.' . $auth->role) }}</span>
    @endauth
    <span class="text-primary ml-4">
        {{ __('labels.time') }}: <span class="font-weight-bold" id="globalTime"></span>
    </span>
</div>
