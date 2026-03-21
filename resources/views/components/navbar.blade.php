<header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
    
    <h2 class="text-lg font-semibold text-gray-700">
        @yield('page-title', 'Dashboard')
    </h2>

    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-500">
            {{ auth()->user()->name ?? '' }}
        </span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="text-sm text-red-500 hover:text-red-700">
                Logout
            </button>
        </form>
    </div>

</header>