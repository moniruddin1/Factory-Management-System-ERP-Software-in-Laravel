<x-app-layout>
    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Production Issue Vouchers</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">List of all materials issued to the factory</p>
                </div>
                <a href="{{ route('inventory.issue.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i> New Issue
                </a>
            </div>

            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300">
                        <thead class="text-xs text-gray-500 dark:text-slate-400 uppercase bg-gray-50 dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Voucher No</th>
                            <th class="px-6 py-4">Issued To (Staff)</th>
                            <th class="px-6 py-4 text-center">Total Items</th>
                            <th class="px-6 py-4">Created By</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                        @forelse($issues as $issue)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20">
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($issue->date)->format('d M, Y') }}</td>
                                <td class="px-6 py-4 font-medium text-blue-600 dark:text-blue-400">{{ $issue->voucher_no }}</td>
                                <td class="px-6 py-4">{{ $staffs[$issue->issued_to] ?? 'Unknown Staff' }}</td>
                                <td class="px-6 py-4 text-center font-bold">{{ $issue->items()->count() }}</td>
                                <td class="px-6 py-4 text-xs">{{ optional($issue->creator)->name ?? 'System' }}</td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <a href="{{ route('inventory.issue.show', $issue->id) }}" class="text-gray-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 bg-gray-100 hover:bg-blue-50 dark:bg-slate-700 dark:hover:bg-slate-600 px-3 py-1.5 rounded transition">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                                    No issue vouchers found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($issues->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                        {{ $issues->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
