<x-app-layout>
    <x-slot name="header">Product Variations (Size & Color)</x-slot>

    <div x-data="{ showAddForm: false, showEditModal: false, editData: { id: '', name: '', type: 'Size', value: '', is_active: true } }" class="space-y-6">

        @can('manage-variations')
            <div x-show="showAddForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-4" class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 relative" style="display: none;">

                <button @click="showAddForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>

                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6"><i class="fa-solid fa-plus-circle text-blue-500 mr-2"></i>Create New Variation</h3>

                <form action="{{ route('variations.store') }}" method="POST" x-data="{ addType: 'Size' }">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Variation Type</label>
                            <select name="type" x-model="addType" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition">
                                <option value="Size">Size (সাইজ)</option>
                                <option value="Color">Color (রঙ)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Name</label>
                            <input type="text" name="name" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition" placeholder="e.g. 42 or Black" required>
                        </div>
                        <div x-show="addType === 'Color'">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Color Hex Code (Optional)</label>
                            <input type="color" name="value" class="w-full h-11 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-xl cursor-pointer p-1 shadow-sm transition">
                        </div>
                    </div>

                    <div class="mb-6 flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-slate-900" checked>
                        <label for="is_active" class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Active</label>
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-blue-500/30">Save Variation</button>
                </form>
            </div>
        @endcan

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 flex flex-col lg:flex-row justify-between items-center gap-4">

                <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300">
                    <span>Show</span>
                    <form action="{{ route('variations.index') }}" method="GET" id="perPageForm">
                        <select name="per_page" onchange="this.form.submit()" class="bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-lg py-1.5 px-3 shadow-sm transition text-sm">
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    <span>entries</span>
                </div>

                <div class="flex flex-col sm:flex-row w-full lg:w-auto items-center gap-3">
                    <form action="{{ route('variations.index') }}" method="GET" class="flex w-full sm:w-auto gap-2">
                        <div class="relative w-full sm:w-48">
                            <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="pl-9 w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-lg focus:ring-1 focus:ring-blue-500 py-1.5 text-sm shadow-sm">
                        </div>
                        <select name="type" onchange="this.form.submit()" class="w-full sm:w-36 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-lg py-1.5 text-sm">
                            <option value="">All Types</option>
                            <option value="Size" {{ request('type') == 'Size' ? 'selected' : '' }}>Size</option>
                            <option value="Color" {{ request('type') == 'Color' ? 'selected' : '' }}>Color</option>
                        </select>
                    </form>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button onclick="window.print()" class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 py-1.5 px-4 rounded-lg transition shadow-sm text-sm"><i class="fa-solid fa-print"></i></button>
                        @can('manage-variations')
                            <button @click="showAddForm = !showAddForm" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-4 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-plus"></i> Add New
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-gray-50/80 dark:bg-slate-800/80 text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase border-y border-gray-200 dark:border-slate-700">
                        <th class="py-3 px-5 w-16 text-center">#</th>
                        <th class="py-3 px-5">Variation Name</th>
                        <th class="py-3 px-5">Type</th>
                        <th class="py-3 px-5">Value/Preview</th>
                        <th class="py-3 px-5">Status</th>
                        @if(auth()->user()->can('manage-variations') || auth()->user()->can('delete-variations'))
                            <th class="py-3 px-5 w-24 text-right">Action</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                    @forelse($variations as $index => $variation)
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-slate-700/30 transition-colors group text-sm">
                            <td class="py-2.5 px-5 text-center text-gray-500">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-5 font-medium text-gray-800 dark:text-gray-200">{{ $variation->name }}</td>
                            <td class="py-2.5 px-5">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium border {{ $variation->type == 'Size' ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20' : 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/20' }}">
                                    {{ $variation->type }}
                                </span>
                            </td>
                            <td class="py-2.5 px-5">
                                @if($variation->type == 'Color')
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-full border border-gray-200 shadow-sm" style="background-color: {{ $variation->value }}"></div>
                                        <span class="text-xs font-mono text-gray-500">{{ $variation->value ?? '-' }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-5">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium border {{ $variation->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400' }}">
                                    {{ $variation->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            @if(auth()->user()->can('manage-variations') || auth()->user()->can('delete-variations'))
                                <td class="py-2.5 px-5 text-right">
                                    <div class="flex justify-end gap-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                        @can('manage-variations')
                                            <button @click="editData = { id: {{ $variation->id }}, name: '{{ $variation->name }}', type: '{{ $variation->type }}', value: '{{ $variation->value }}', is_active: {{ $variation->is_active ? 'true' : 'false' }} }; showEditModal = true" class="text-blue-600 hover:text-blue-800 dark:text-blue-400"><i class="fa-solid fa-pen-to-square"></i></button>
                                        @endcan

                                        @can('delete-variations')
                                            <form action="{{ route('variations.destroy', $variation->id) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)" class="text-red-500"><i class="fa-solid fa-trash-can"></i></button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-gray-400">No variations found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($variations->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-slate-700">
                    {{ $variations->links() }}
                </div>
            @endif
        </div>

        @can('manage-variations')
            <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-500/75 dark:bg-slate-900/80 transition-opacity" @click="showEditModal = false"></div>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full z-50 border border-gray-100 dark:border-slate-700">
                        <form x-bind:action="`/variations/${editData.id}`" method="POST">
                            @csrf @method('PUT')
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between">
                                <h3 class="font-bold text-gray-900 dark:text-white">Edit Variation</h3>
                                <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Variation Type</label>
                                    <select name="type" x-model="editData.type" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500">
                                        <option value="Size">Size</option>
                                        <option value="Color">Color</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                    <input type="text" name="name" x-model="editData.name" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div x-show="editData.type === 'Color'">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Color Preview</label>
                                    <input type="color" name="value" x-model="editData.value" class="w-full h-11 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-xl">
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="edit_is_active" x-model="editData.is_active" class="rounded border-gray-300 text-blue-600">
                                    <label for="edit_is_active" class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Active Status</label>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-slate-800/50 px-6 py-4 flex justify-end gap-3">
                                <button type="button" @click="showEditModal = false" class="bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 py-2 px-4 rounded-xl transition">Cancel</button>
                                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-xl transition shadow-lg shadow-blue-500/30">Update</button>
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
                text: "Deleting this might affect products using it!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000'
            }).then((result) => { if (result.isConfirmed) button.closest('form').submit(); });
        }
    </script>
</x-app-layout>
