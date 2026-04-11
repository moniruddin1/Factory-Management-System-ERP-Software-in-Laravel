<x-app-layout>
    <x-slot name="header">Unit Management</x-slot>

    <div x-data="{ showAddForm: false, showEditModal: false, editData: { id: '', name: '', short_name: '', is_active: true } }" class="space-y-6">

        @can('manage-units')
            <div x-show="showAddForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-4" class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 transition-colors duration-300 relative" style="display: none;">

                <button @click="showAddForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>

                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6">
                    <i class="fa-solid fa-plus-circle text-blue-500 mr-2"></i>Create New Unit
                </h3>

                <form action="{{ route('units.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Unit Name</label>
                            <input type="text" name="name" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition" placeholder="e.g. Kilogram" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Short Name</label>
                            <input type="text" name="short_name" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition" placeholder="e.g. KG" required>
                        </div>
                    </div>

                    <div class="mb-6 flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-900 dark:border-slate-600" checked>
                        <label for="is_active" class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Active Status</label>
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-blue-500/30">
                        Save Unit
                    </button>
                </form>
            </div>
        @endcan

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 transition-colors duration-300 overflow-hidden">

            <div class="p-5 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 flex flex-col lg:flex-row justify-between items-center gap-4">

                <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300">
                    <span>Show</span>
                    <select class="bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-lg focus:ring-1 focus:ring-blue-500 py-1.5 px-3 shadow-sm transition text-sm">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span>entries</span>
                </div>

                <div class="flex flex-col sm:flex-row w-full lg:w-auto items-center gap-3">

                    <div class="relative w-full sm:w-48">
                        <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                        <input type="text" placeholder="Search units..." class="pl-9 w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-lg focus:ring-1 focus:ring-blue-500 py-1.5 text-sm shadow-sm transition">
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button onclick="window.print()" class="flex-1 sm:flex-none bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 font-medium py-1.5 px-4 rounded-lg transition shadow-sm text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-print"></i> <span class="hidden sm:inline">Print</span>
                        </button>

                        @can('manage-units')
                            <button @click="showAddForm = !showAddForm" class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-4 rounded-lg transition shadow-sm flex items-center justify-center gap-2 text-sm">
                                <i class="fa-solid fa-plus"></i> Add New
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-gray-50/80 dark:bg-slate-800/80 text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wider border-y border-gray-200 dark:border-slate-700">
                        <th class="py-3 px-5 w-16 text-center">#</th>
                        <th class="py-3 px-5">Unit Name</th>
                        <th class="py-3 px-5 w-40">Short Name</th>
                        <th class="py-3 px-5 w-32">Status</th>
                        @if(auth()->user()->can('manage-units') || auth()->user()->can('delete-units'))
                            <th class="py-3 px-5 w-24 text-right">Action</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                    @forelse($units as $index => $unit)
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-slate-700/30 transition-colors group">
                            <td class="py-2.5 px-5 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-2.5 px-5 font-medium text-gray-800 dark:text-gray-200 text-sm">
                                {{ $unit->name }}
                            </td>
                            <td class="py-2.5 px-5 text-gray-500 dark:text-gray-400 text-sm">
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 font-mono text-xs">
                                        {{ $unit->short_name }}
                                    </span>
                            </td>
                            <td class="py-2.5 px-5">
                                @if($unit->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/50">
                                            Active
                                        </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-medium border bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/50">
                                            Inactive
                                        </span>
                                @endif
                            </td>
                            @if(auth()->user()->can('manage-units') || auth()->user()->can('delete-units'))
                                <td class="py-2.5 px-5 text-right">
                                    <div class="flex justify-end gap-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                        @can('manage-units')
                                            <button type="button" @click="editData = { id: {{ $unit->id }}, name: '{{ addslashes($unit->name) }}', short_name: '{{ addslashes($unit->short_name) }}', is_active: {{ $unit->is_active ? 'true' : 'false' }} }; showEditModal = true" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        @endcan

                                        @can('delete-units')
                                            <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition" title="Delete">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                    <i class="fa-solid fa-scale-unbalanced text-4xl mb-3"></i>
                                    <p class="text-sm">No units found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @can('manage-units')
            <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                    <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500/75 dark:bg-slate-900/80 transition-opacity" @click="showEditModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-100 dark:border-slate-700">

                        <form x-bind:action="`/units/${editData.id}`" method="POST">
                            @csrf @method('PUT')
                            <div class="px-6 pt-6 pb-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">Edit Unit</h3>
                                <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                            </div>

                            <div class="px-6 py-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Unit Name</label>
                                    <input type="text" name="name" x-model="editData.name" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Short Name</label>
                                    <input type="text" name="short_name" x-model="editData.short_name" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition" required>
                                </div>
                                <div class="flex items-center pt-2">
                                    <input type="checkbox" name="is_active" id="edit_is_active" x-model="editData.is_active" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-900 dark:border-slate-600">
                                    <label for="edit_is_active" class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Active Status</label>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-slate-800/50 px-6 py-4 flex justify-end space-x-3 rounded-b-2xl">
                                <button type="button" @click="showEditModal = false" class="bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600 font-medium py-2 px-4 rounded-xl transition">Cancel</button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-xl transition shadow-lg shadow-blue-500/30">Update Unit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

    </div>

    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>
</x-app-layout>
