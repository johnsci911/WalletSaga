<nav x-data="{ open: false }" class="bg-slate-950 text-slate-300 shadow fixed w-full z-20 top-0 start-0">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-16">
            <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <!-- Mobile menu button -->
                <button @click="open = !open" type="button" class="inline-flex items-center justify-center p-2 rounded-md hover:hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-slate-800" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 flex items-center justify-center sm:items-stretch sm:justify-start">
                <div class="flex-shrink-0 flex items-center">
                    <!-- You can add your logo here -->
                    <span class="text-lg font-bold">WalletSaga</span>
                </div>
                <div class="hidden sm:block sm:ml-6">
                    <div class="flex space-x-4">
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'bg-slate-700 hover:text-white' : 'hover:text-white hover:bg-gray-800' }} px-3 py-2 rounded-md text-sm font-medium">Home</a>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-slate-700 hover:text-white' : 'hover:text-white hover:bg-gray-800' }} px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                    </div>
                </div>
            </div>
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                @if (Auth::check())
                    <div class="hidden sm:flex items-center space-x-4">
                        <p class="text-sm">Welcome, {{ Auth::user()->name }}!</p>
                        <a href="{{ route('profile.show') }}" class="text-sm font-medium hover:text-white">Profile Settings</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-400">Logout</button>
                        </form>
                    </div>
                @else
                    <div class="hidden sm:block">
                        <a href="{{ route('register') }}" class="text-sm font-medium text-blue-400 hover:text-blue-200">Register</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="sm:hidden" id="mobile-menu" x-show="open" x-cloak>
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'bg-slate-800 text-slate-300 hover:text-white' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Home</a>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-slate-800 text-slate-300 hover:text-white' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }} block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
            @if (Auth::check())
                <a href="{{ route('profile.show') }}" class="text-slate-300 hover:bg-slate-900 hover:text-white block px-3 py-2 rounded-md text-base font-medium">Profile Settings</a>
                <form action="{{ route('logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-400 hover:bg-slate-800 block w-full text-left px-3 py-2 rounded-md text-base font-medium">Logout</button>
                </form>
            @else
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-400 hover:bg-slate-800 block px-3 py-2 rounded-md text-base font-medium">Register</a>
            @endif
        </div>
    </div>
</nav>
