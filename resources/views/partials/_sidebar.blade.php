<div class="border-right" id="sidebar-wrapper">
    <a href="{{ url('/') }}">
        <h1 class="sidebar-heading h4">
            {{ config('app.name', 'Laravel') }}
        </h1>
    </a>
    <div class="list-group list-group-flush">
        <a href="#" class="list-group-item list-group-item-action">
            <ion-icon name="people-outline"></ion-icon>
            Users
        </a>
        <a href="#" class="list-group-item list-group-item-action">
            <ion-icon name="people-outline"></ion-icon>
            Users
        </a>
        <a href="#" class="list-group-item list-group-item-action">
            <ion-icon name="people-outline"></ion-icon>
            Users
        </a>
        <a href="#" class="list-group-item list-group-item-action"
           onclick="event.preventDefault(); document.getElementById('logoutForm').submit()">
            <ion-icon name="log-out-outline"></ion-icon>
            Log Out
            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                @csrf
            </form>
        </a>
    </div>
</div>
