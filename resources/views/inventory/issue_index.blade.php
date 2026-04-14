<x-app-layout>
    <div class="py-6 bg-gray-50 dark:bg-[#0f172a] min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Production Issue Vouchers</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Track materials from issue to final production</p>
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
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Items</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                        @forelse($issues as $issue)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/20">
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($issue->date)->format('d M, Y') }}</td>
                                <td class="px-6 py-4 font-medium text-blue-600 dark:text-blue-400">{{ $issue->voucher_no }}</td>

                                {{-- স্টাফের নাম এবং আইডি --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $staffs[$issue->issued_to] ?? 'Unknown Staff' }}</span>
                                        <span class="text-[10px] text-gray-400">ID: #{{ $issue->issued_to }}</span>
                                    </div>
                                </td>

                                {{-- স্ট্যাটাস লজিক --}}
                                <td class="px-6 py-4 text-center">
                                    @if($issue->production)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            <i class="fa-solid fa-circle-check mr-1.5"></i> Completed
                                        </span>
                                        <div class="text-[10px] text-gray-400 mt-1">Ref: {{ $issue->production->reference_no }}</div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                            <i class="fa-solid fa-clock mr-1.5"></i> In Factory (WIP)
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center font-bold">{{ $issue->items()->count() }}</td>

                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <a href="{{ route('inventory.issue.show', $issue->id) }}" class="text-gray-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 bg-gray-100 hover:bg-blue-50 dark:bg-slate-700 dark:hover:bg-slate-600 px-3 py-1.5 rounded transition shadow-sm">
                                        <i class="fa-solid fa-eye"></i> Details
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
            </div>
        </div>
    </div>
</x-app-layout>
