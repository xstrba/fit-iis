<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ config('app.name', 'Laravel') }}
        @isset($title)
            | {{ $title }}
        @endisset
    </title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @notifyCss
</head>
<body>
<div id="app">
    <div class="d-flex" id="wrapper">
        @auth
            @include('partials._sidebar')
        @endauth

        <main>
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
            <div class="pt-5">
                <div class="main-content py-5 px-4">
                    <h2 class="h2 page-title">{{ $title ?? __('pages.dashboard') }}</h2>
                    <div class="pt-5">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@include('notify::messages')

@stack('scripts')
@notifyJs
<!-- Icons -->
<script type="module" src="https://unpkg.com/ionicons@5.2.3/dist/ionicons/ionicons.esm.js"></script>
</body>
</html>
