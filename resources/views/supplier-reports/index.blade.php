<x-app-layout>
    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans transition-colors duration-200">
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex items-center gap-3 text-gray-800 dark:text-white">
                <div class="bg-indigo-600 p-2.5 rounded-lg text-white shadow-lg">
                    <i class="fa-solid fa-chart-pie text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Supplier Reports</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Comprehensive overview of supplier transactions and balances</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Due Balance Report Card --}}
                <a href="{{ route('supplier-reports.due-report') }}" class="group bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 p-3 rounded-lg">
                            <i class="fa-solid fa-scale-unbalanced text-2xl"></i>
                        </div>
                        <i class="fa-solid fa-arrow-right text-gray-300 dark:text-slate-600 group-hover:text-indigo-500 transition-colors"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Due & Balance List</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400">View total payables and advance balances for all suppliers at a glance.</p>
                </a>

                {{-- Future Reports (Placeholder) --}}
                <a href="{{ route('supplier-reports.purchase-report') }}" class="group bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 hover:border-emerald-500 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 p-3 rounded-lg">
                            <i class="fa-solid fa-cart-shopping text-2xl"></i>
                        </div>
                        <i class="fa-solid fa-arrow-right text-gray-300 dark:text-slate-600 group-hover:text-emerald-500 transition-colors"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Purchase Summary</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Date-wise total purchase summary.</p>
                </a>

                <a href="{{ route('supplier-reports.payment-report') }}" class="block bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-200 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-3 rounded-lg group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                            <i class="fa-solid fa-money-bill-wave text-2xl"></i>
                        </div>
                        <div class="text-gray-300 dark:text-slate-600 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-transform duration-300 group-hover:translate-x-1">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Payment Summary</h3>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Date-wise total payment records to suppliers.</p>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
