<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
        <div class="relative flex items-center h-16 w-full">
            <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <!-- Mobile menu button-->
            </div>
            <div class="flex flex-row items-center justify-between w-full">
                <div class="flex space-x-4">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-blue-600 bg-gray-200' : 'text-gray-900 hover:bg-gray-200' }} px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-blue-600 bg-gray-200' : 'text-gray-900 hover:bg-gray-200' }} px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                </div>
                @if (Auth::check())
                    <div class="flex items-center space-x-4">
                        <p>Welcome, {{ Auth::user()->name }}!</p>
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary">Profile Settings</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>
                @else
                    <div>
                        <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</nav>

