<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
     class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 text-gray-800 dark:text-white border-r border-gray-200 dark:border-slate-800 transition-all duration-300 transform md:relative md:translate-x-0 flex flex-col shadow-xl">

    <div class="p-5 flex items-center justify-between border-b border-gray-200 dark:border-slate-800">
        <span class="text-xl font-bold tracking-wider">SHOE <span class="text-blue-600 dark:text-blue-500">ERP</span></span>
        <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition {{ request()->routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-600 dark:text-gray-300' }}">
            <i class="fa-solid fa-gauge w-8 text-center"></i> Dashboard
        </a>

        @if(auth()->user()->can('view-categories') || auth()->user()->can('view-units') || auth()->user()->can('view-variations'))
            <div x-data="{ open: {{ request()->routeIs('units.*') || request()->routeIs('categories.*') || request()->routeIs('variations.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-300">
                    <div class="flex items-center">
                        <i class="fa-solid fa-gears w-8 text-center text-blue-500 dark:text-blue-400"></i> <span class="font-medium">Master Setup</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-cloak class="ml-8 mt-2 space-y-1 border-l-2 border-gray-200 dark:border-slate-700 pl-4">
                    @can('view-categories')
                        <a href="{{ route('categories.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('categories.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Category List</a>
                    @endcan

                    @can('view-units')
                        <a href="{{ route('units.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('units.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Unit List</a>
                    @endcan

                    @can('view-variations')
                        <a href="{{ route('variations.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('variations.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Variation List</a>
                    @endcan
                </div>
            </div>
        @endif

        @if(auth()->user()->can('view-roles') || auth()->user()->can('view-users'))
            @if(auth()->user()->can('view-users') || auth()->user()->can('manage-roles'))
                <div x-data="{ open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <div class="flex items-center">
                            <i class="fa-solid fa-shield-halved w-6 text-blue-500"></i>
                            <span class="ml-3 font-semibold">User Access</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-cloak class="bg-gray-50 dark:bg-slate-900/50">
                        @can('view-users')
                            <a href="{{ route('users.index') }}" class="flex items-center pl-12 py-2 text-sm {{ request()->routeIs('users.index') ? 'text-blue-600 font-bold' : 'text-gray-500' }}">
                                <i class="fa-solid fa-users-gear mr-2"></i> User List
                            </a>
                        @endcan

                        @can('manage-roles')
                            <a href="{{ route('roles.index') }}" class="flex items-center pl-12 py-2 text-sm text-gray-500 hover:text-blue-500">
                                <i class="fa-solid fa-user-lock mr-2"></i> Role & Permissions
                            </a>
                        @endcan
                    </div>
                </div>
            @endif
        @endif
        <a href="{{ route('audit-logs.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('audit-logs.*') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-slate-700/50 hover:text-gray-900 dark:hover:text-white' }}">

            <i class="fa-solid fa-clock-rotate-left text-lg {{ request()->routeIs('audit-logs.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }}"></i>

            <span>Audit Logs</span>
        </a>
        @if(auth()->user()->can('view-raw-materials') || auth()->user()->can('view-finished-goods'))
            <div x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-300">
                    <div class="flex items-center">
                        <i class="fa-solid fa-boxes-stacked w-8 text-center text-green-500 dark:text-green-400"></i> <span class="font-medium">Inventory</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-cloak class="ml-8 mt-2 space-y-1 border-l-2 border-gray-200 dark:border-slate-700 pl-4">
                    @can('view-raw-materials')
                        <a href="#" class="block p-2 text-sm rounded-lg text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">Raw Materials</a>
                    @endcan
                    @can('view-finished-goods')
                        <a href="#" class="block p-2 text-sm rounded-lg text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">Finished Goods</a>
                    @endcan
                </div>
            </div>
        @endif
    </nav>
</div>
