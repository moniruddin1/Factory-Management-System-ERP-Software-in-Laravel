<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-blue-500"></i> Audit Logs
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track all system activities and data changes.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-5">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 xl:grid-cols-6 gap-4 items-end">

                <div class="md:col-span-2 xl:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-1.5">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user, module..."
                               class="pl-10 w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm transition-shadow">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-1.5">Action Type</label>
                    <select name="event" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('event') == 'all' ? 'selected' : '' }}>All Actions</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-1.5">Date</label>
                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()"
                           class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-1.5">Show Rows</label>
                    <select name="per_page" class="w-full rounded-lg border-gray-200 dark:border-slate-600 dark:bg-slate-900 dark:text-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm" onchange="this.form.submit()">
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Records</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Records</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Records</option>
                        <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250 Records</option>
                    </select>
                </div>

                <div class="flex gap-2 h-[38px]">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'event', 'date']))
                        <a href="{{ route('audit-logs.index') }}" class="px-3 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 text-sm font-medium rounded-lg transition-colors flex items-center justify-center tooltip" title="Clear Filters">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden relative">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-white dark:from-slate-800/80 dark:to-slate-800/50 text-gray-500 dark:text-gray-400 text-xs font-bold uppercase tracking-wider border-y border-gray-200 dark:border-slate-700">
                        <th class="py-4 px-6 text-center w-16">#</th>
                        <th class="py-4 px-6">Timestamp</th>
                        <th class="py-4 px-6">User</th>
                        <th class="py-4 px-6">Module & Action</th>
                        <th class="py-4 px-6 w-5/12">Data Changes</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                    @forelse($logs as $index => $log)
                        <tr class="hover:bg-blue-50/30 dark:hover:bg-slate-700/30 transition-all duration-200 text-sm group">
                            <td class="py-4 px-6 text-center text-gray-400 font-medium group-hover:text-blue-500">{{ $logs->firstItem() + $index }}</td>

                            <td class="py-4 px-6 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $log->created_at->format('d M, Y') }}</div>
                                <div class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                                    <i class="fa-regular fa-clock"></i> {{ $log->created_at->format('h:i:s A') }}
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ $log->causer ? substr($log->causer->name, 0, 2) : 'SY' }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 dark:text-gray-200">{{ $log->causer ? $log->causer->name : 'System / Guest' }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $log->causer ? $log->causer->email : 'Auto generated' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                <div class="mb-1.5 flex items-center gap-2">
                                    @if($log->event == 'created')
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800/50 shadow-sm">Created</span>
                                    @elseif($log->event == 'updated')
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50 shadow-sm">Updated</span>
                                    @elseif($log->event == 'deleted')
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800/50 shadow-sm">Deleted</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide bg-gray-100 text-gray-700 border border-gray-300 shadow-sm">{{ $log->event }}</span>
                                    @endif
                                </div>
                                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-cube text-gray-400"></i> {{ class_basename($log->subject_type) }} <span class="text-gray-300">|</span> ID: {{ $log->subject_id }}
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                @if($log->properties && isset($log->properties['attributes']))
                                    <div class="space-y-2 text-[11px] bg-gray-50/50 dark:bg-slate-900/50 p-3 rounded-xl border border-gray-100 dark:border-slate-700">
                                        @foreach($log->properties['attributes'] as $key => $value)
                                            @if(!in_array($key, ['password', 'remember_token', 'updated_at', 'created_at']))
                                                <div class="flex flex-col xl:flex-row xl:items-center gap-1 xl:gap-3 py-1 border-b border-gray-100/50 dark:border-slate-700/50 last:border-0 last:pb-0">
                                                    <div class="xl:w-1/4 text-gray-500 font-bold uppercase tracking-wide">{{ str_replace('_', ' ', $key) }}:</div>
                                                    <div class="xl:w-3/4 flex flex-wrap items-center gap-2">
                                                        @if(isset($log->properties['old']) && isset($log->properties['old'][$key]) && $log->properties['old'][$key] != $value)
                                                            <span class="line-through text-rose-400 bg-rose-50 dark:bg-rose-900/20 px-1.5 py-0.5 rounded">{{ is_array($log->properties['old'][$key]) ? json_encode($log->properties['old'][$key]) : (empty($log->properties['old'][$key]) ? 'null' : (string)$log->properties['old'][$key]) }}</span>
                                                            <i class="fa-solid fa-arrow-right text-gray-400 text-[10px]"></i>
                                                        @endif
                                                        <span class="text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-1.5 py-0.5 rounded">{{ is_array($value) ? json_encode($value) : (empty($value) && $value !== '0' ? 'null' : (string)$value) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 italic bg-gray-50/50 dark:bg-slate-900/50 p-2 rounded-lg text-center border border-dashed border-gray-200 dark:border-slate-700">
                                        No detailed changes recorded.
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-search text-2xl opacity-40"></i>
                                    </div>
                                    <p class="font-medium text-gray-500">No activity logs found matching your criteria.</p>
                                    <a href="{{ route('audit-logs.index') }}" class="text-blue-500 hover:underline text-sm mt-2">Clear all filters</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-slate-700 bg-gray-50/30 dark:bg-slate-800/30">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
