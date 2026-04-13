<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
     class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 text-gray-800 dark:text-white border-r border-gray-200 dark:border-slate-800 transition-all duration-300 transform md:relative md:translate-x-0 flex flex-col shadow-xl">

    <div class="p-5 flex items-center justify-between border-b border-gray-200 dark:border-slate-800">
        <span class="text-xl font-bold tracking-wider uppercase">Shoe <span class="text-blue-600 dark:text-blue-500">ERP</span></span>
        <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition {{ request()->routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-600 dark:text-gray-300' }}">
            <i class="fa-solid fa-gauge w-8 text-center text-lg"></i> Dashboard
        </a>

        @if(auth()->user()->can('view-categories') || auth()->user()->can('view-units') || auth()->user()->can('view-products'))
            <div x-data="{ open: {{ request()->routeIs('units.*') || request()->routeIs('categories.*') || request()->routeIs('products.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-300">
                    <div class="flex items-center">
                        <i class="fa-solid fa-gears w-8 text-center text-blue-500 dark:text-blue-400"></i>
                        <span class="font-medium">Master Setup</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-cloak class="ml-8 mt-2 space-y-1 border-l-2 border-gray-200 dark:border-slate-700 pl-4">
                    @can('view-categories')
                        <a href="{{ route('categories.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('categories.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Category List</a>
                    @endcan

                    @can('view-units')
                        <a href="{{ route('units.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('units.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Unit List</a>
                    @endcan

                    <a href="{{ route('products.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('products.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Product List</a>
                </div>
            </div>
        @endif

        <div x-data="{ open: {{ request()->routeIs(['suppliers.*', 'supplier-products.*', 'purchases.*', 'supplier-supplier-payments.*', 'purchase-returns.*', 'supplier-ledgers.*', 'supplier-reports.*']) ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-300">
                <div class="flex items-center">
                    <i class="fa-solid fa-truck-moving w-8 text-center text-purple-500 dark:text-purple-400"></i>
                    <span class="font-medium">Supplier & Purchase</span>
                </div>
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" x-cloak class="ml-8 mt-2 space-y-1 border-l-2 border-gray-200 dark:border-slate-700 pl-4">
                <a href="{{ route('suppliers.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('suppliers.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
                    Suppliers Profile
                </a>

                <a href="{{ route('supplier-products.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('supplier-products.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
                    Supplier Items
                </a>

                <a href="{{ route('purchases.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('purchases.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
                    Purchase Invoices
                </a>

                <a href="{{ route('supplier-payments.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('supplier-supplier-payments.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
                    Payments & Dues
                </a>

                <a href="{{ route('purchase-returns.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('purchase-returns.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
                    Purchase Returns
                </a>

                <a href="{{ route('supplier-ledgers.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('supplier-ledgers.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
                    Supplier Ledger
                </a>

                <a href="{{ route('supplier-reports.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('supplier-reports.*') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">
                    Reports & Statements
                </a>
            </div>
        </div>

        <div x-data="{ open: false }">
            <button @click="open = !open" class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-300">
                <div class="flex items-center">
                    <i class="fa-solid fa-boxes-stacked w-8 text-center text-green-500 dark:text-green-400"></i>
                    <span class="font-medium">Inventory & Mfg</span>
                </div>
                <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" x-cloak class="ml-8 mt-2 space-y-1 border-l-2 border-gray-200 dark:border-slate-700 pl-4">
                <a href="#" class="block p-2 text-sm rounded-lg text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white italic opacity-70">Stock (FIFO) - Coming</a>
                <a href="#" class="block p-2 text-sm rounded-lg text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white italic opacity-70">Supervisor Transfers</a>
                <a href="#" class="block p-2 text-sm rounded-lg text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white italic opacity-70">Production Receive</a>
            </div>
        </div>

        @if(auth()->user()->can('view-roles') || auth()->user()->can('view-users'))
            <div x-data="{ open: {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition text-gray-600 dark:text-gray-300">
                    <div class="flex items-center">
                        <i class="fa-solid fa-shield-halved w-8 text-center text-orange-500"></i>
                        <span class="font-medium">User Access</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-cloak class="ml-8 mt-2 space-y-1 border-l-2 border-gray-200 dark:border-slate-700 pl-4">
                    @can('view-users')
                        <a href="{{ route('users.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('users.index') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">User List</a>
                    @endcan

                    @can('manage-roles')
                        <a href="{{ route('roles.index') }}" class="block p-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 {{ request()->routeIs('roles.index') ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-500 dark:text-gray-400' }}">Roles & Permissions</a>
                    @endcan
                </div>
            </div>
        @endif

        <a href="{{ route('audit-logs.index') }}"
           class="flex items-center p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition {{ request()->routeIs('audit-logs.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-bold' : 'text-gray-600 dark:text-gray-300' }}">
            <i class="fa-solid fa-clock-rotate-left w-8 text-center text-lg"></i> Audit Logs
        </a>
    </nav>

    <div class="p-4 border-t border-gray-200 dark:border-slate-800">
        <p class="text-[10px] text-center text-gray-400 uppercase font-bold tracking-widest leading-none">Logged in as</p>
        <p class="text-sm text-center font-medium text-gray-600 dark:text-gray-300 mt-1">{{ auth()->user()->name }}</p>
    </div>
</div>
