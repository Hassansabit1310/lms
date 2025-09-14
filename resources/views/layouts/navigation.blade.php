<nav x-data="{ open: false }" class="bg-white/98 backdrop-blur-lg border-b border-gray-200/60 sticky top-0 z-50 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="text-white font-bold text-xl">E</span>
                    </div>
                    <span class="font-black text-xl text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                        EduVerse
                    </span>
                </a>
            </div>

            <!-- Centered Navigation Links -->
            <div class="hidden sm:flex items-center justify-center flex-1">
                <div class="flex space-x-1">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('home') ? 'bg-slate-100 text-slate-800' : 'text-gray-700 hover:text-slate-800 hover:bg-slate-50' }}">
                        🏠 Home
                    </a>
                    <a href="{{ route('courses.index') }}" class="px-4 py-2 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('courses.*') ? 'bg-slate-100 text-slate-800' : 'text-gray-700 hover:text-slate-800 hover:bg-slate-50' }}">
                        📚 Courses
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-slate-100 text-slate-800' : 'text-gray-700 hover:text-slate-800 hover:bg-slate-50' }}">
                        🎯 Categories
                    </a>
                    @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-slate-800' : 'text-gray-700 hover:text-slate-800 hover:bg-slate-50' }}">
                        ⚡ Dashboard
                    </a>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white/90 hover:text-slate-800 hover:bg-white focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @if(auth()->user()->role === 'admin')
                            <div class="border-t border-gray-100 my-1"></div>
                            <div class="px-4 py-2 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                                Admin Panel
                            </div>
                            <x-dropdown-link :href="route('admin.dashboard')">
                                🛠️ Admin Dashboard
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.courses.index')">
                                📚 Manage Courses
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.users.index')">
                                👥 Manage Users
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.h5p.index')">
                                🧩 H5P Content
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <div class="flex items-center space-x-3">
                    <a href="{{ route('login') }}" class="text-slate-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                        👤 Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-pink-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                        🚀 Register
                    </a>
                </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Home') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">
                {{ __('Courses') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                {{ __('Categories') }}
            </x-responsive-nav-link>
            @auth
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @endauth
        </div>

        @auth
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-slate-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <!-- Guest Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Login') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            </div>
        </div>
        @endauth
    </div>
</nav>
