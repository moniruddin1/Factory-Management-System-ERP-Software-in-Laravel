<header class="bg-white dark:bg-slate-800 border-b dark:border-slate-700 h-16 flex items-center justify-between px-6 shadow-sm">
    <div class="flex items-center space-x-4">
        <button @click="sidebarOpen = true" class="md:hidden text-gray-600 dark:text-gray-300">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
        <h1 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
            {{ $header ?? 'System Dashboard' }}
        </h1>
    </div>

    <div class="flex items-center space-x-6">
        <button @click="darkMode = !darkMode; localStorage.setItem('dark', darkMode)" class="text-gray-500 dark:text-yellow-400 hover:scale-110 transition">
            <i :class="darkMode ? 'fa-solid fa-sun' : 'fa-solid fa-moon'" class="text-xl"></i>
        </button>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none group">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold leading-none text-gray-800 dark:text-white group-hover:text-blue-600 transition-colors">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-[10px] font-bold text-blue-500 dark:text-blue-400 uppercase tracking-tighter mt-1 bg-blue-50 dark:bg-blue-900/20 px-1 rounded">
                        {{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}
                    </p>
                </div>

                @if(Auth::user()->picture)
                    <img src="{{ asset('storage/' . Auth::user()->picture) }}" class="h-10 w-10 rounded-full border-2 border-blue-500 shadow-sm object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff" class="h-10 w-10 rounded-full border-2 border-blue-500 shadow-sm">
                @endif
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="open = false" x-cloak
                 class="absolute right-0 mt-3 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 py-2 z-50">

                <div class="px-4 py-2 border-b dark:border-slate-700 sm:hidden">
                    <p class="text-sm font-bold dark:text-white">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-blue-500">{{ Auth::user()->getRoleNames()->first() ?? 'No Role' }}</p>
                </div>

                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                    <i class="fa-solid fa-user-gear mr-2 text-blue-500"></i> Profile Settings
                </a>

                <hr class="my-1 dark:border-slate-700">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                        <i class="fa-solid fa-power-off mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
