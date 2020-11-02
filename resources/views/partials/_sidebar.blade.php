@php
/**
 * @var \App\Sidebar\Sidebar $sidebar
 */
@endphp

<div class="collapse" id="sidebar-wrapper">
    <div class="sidebar-placeholder">
    </div>
    <div class="sidebar">
        <a href="{{ url('/') }}">
            <h1 class="sidebar-heading h4">
                {{ config('app.name', 'Laravel') }}
            </h1>
        </a>
        <div class="list-group list-group-flush">
            @foreach($sidebar->getItems() as $item)
                <a href="{{ $item->getLink() }}"
                   class="list-group-item list-group-item-action @if($item->getActive()) active @endif">
                    <i class="fas fa-{{ $item->getIcon() }} sidebar-icon"></i>
                    <span class="sidebar-text">{{ trans($item->getTrKey()) }}</span>
                </a>
            @endforeach

            <a href="#" class="list-group-item list-group-item-action"
               onclick="event.preventDefault(); document.getElementById('logoutForm').submit()">
                <i class="fas fa-sign-out-alt sidebar-icon"></i>
                <span class="sidebar-text">{{ __('labels.logout') }}</span>
                <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                    @csrf
                </form>
            </a>
        </div>
    </div>
</div>
