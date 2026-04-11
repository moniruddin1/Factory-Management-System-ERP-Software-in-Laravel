<x-app-layout>
    <x-slot name="header">Roles & Permissions Management</x-slot>

    <div x-data="{ showAddForm: false, showEditModal: false, editData: { id: '', name: '', permissions: [] } }" class="space-y-6">

        @can('manage-roles')
            <div x-show="showAddForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-4" class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 relative" style="display: none;">

                <button @click="showAddForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>

                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6"><i class="fa-solid fa-shield-halved text-blue-500 mr-2"></i>Create New Role</h3>

                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role Name</label>
                        <input type="text" name="name" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition" placeholder="e.g. Manager" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Assign Permissions</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($permissions as $permission)
                                <label class="flex items-center space-x-3 p-3 border border-gray-100 dark:border-slate-700 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700/50 transition cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-blue-500/30">Save Role</button>
                </form>
            </div>
        @endcan

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Existing Roles</h3>
                @can('manage-roles')
                    <button @click="showAddForm = !showAddForm" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-4 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-plus"></i> Add New Role
                    </button>
                @endcan
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-gray-50/80 dark:bg-slate-800/80 text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase border-y border-gray-200 dark:border-slate-700">
                        <th class="py-3 px-5 w-16 text-center">#</th>
                        <th class="py-3 px-5">Role Name</th>
                        <th class="py-3 px-5">Permissions</th>
                        @if(auth()->user()->can('manage-roles') || auth()->user()->can('delete-roles'))
                            <th class="py-3 px-5 w-24 text-right">Action</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                    @forelse($roles as $index => $role)
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-slate-700/30 transition-colors group text-sm">
                            <td class="py-2.5 px-5 text-center text-gray-500">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-5 font-bold text-gray-800 dark:text-gray-200">{{ $role->name }}</td>
                            <td class="py-2.5 px-5">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($role->permissions as $p)
                                        <span class="px-2 py-0.5 rounded text-[10px] bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-100 dark:border-blue-800">{{ $p->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            @if(auth()->user()->can('manage-roles') || auth()->user()->can('delete-roles'))
                                <td class="py-2.5 px-5 text-right">
                                    <div class="flex justify-end gap-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                        @can('manage-roles')
                                            <button @click="editData = { id: {{ $role->id }}, name: '{{ $role->name }}', permissions: {{ $role->permissions->pluck('name') }} }; showEditModal = true" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen-to-square"></i></button>
                                        @endcan

                                        @can('delete-roles')
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash-can"></i></button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-12 text-center text-gray-400">No roles defined.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @can('manage-roles')
            <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-500/75 dark:bg-slate-900/80 transition-opacity" @click="showEditModal = false"></div>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-xl transform transition-all sm:max-w-2xl w-full z-50 border border-gray-100 dark:border-slate-700">
                        <form x-bind:action="`/roles/${editData.id}`" method="POST">
                            @csrf @method('PUT')
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between">
                                <h3 class="font-bold text-gray-900 dark:text-white">Edit Role: <span x-text="editData.name" class="text-blue-500"></span></h3>
                                <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role Name</label>
                                    <input type="text" name="name" x-model="editData.name" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Permissions</label>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($permissions as $permission)
                                            <label class="flex items-center space-x-2 text-xs">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" :checked="editData.permissions.includes('{{ $permission->name }}')" class="rounded text-blue-600">
                                                <span class="text-gray-600 dark:text-gray-400">{{ $permission->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-slate-800/50 px-6 py-4 flex justify-end gap-3">
                                <button type="button" @click="showEditModal = false" class="bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 py-2 px-4 rounded-xl transition">Cancel</button>
                                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-xl shadow-lg shadow-blue-500/30">Update Role</button>
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
                title: 'Delete Role?',
                text: "This will remove access for all users with this role!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete!',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000'
            }).then((result) => { if (result.isConfirmed) button.closest('form').submit(); });
        }
    </script>
</x-app-layout>
