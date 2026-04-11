<x-app-layout>
    <x-slot name="header">User Management</x-slot>

    <div x-data="{
        showAddForm: false,
        showEditModal: false,
        editData: { id: '', name: '', username: '', email: '', phone: '', job_title: '', role: '' }
    }" class="space-y-6">

        @can('manage-users')
            <div x-show="showAddForm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 relative"
                 style="display: none;">

                <button @click="showAddForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>

                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-6"><i class="fa-solid fa-user-plus text-blue-500 mr-2"></i>Create New User</h3>

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                            <input type="text" name="name" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition" placeholder="Full Name" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Username</label>
                            <input type="text" name="username" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition" placeholder="unique_username" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" name="email" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition" placeholder="email@example.com" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                            <input type="text" name="phone" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition" placeholder="017XXXXXXXX">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Job Title</label>
                            <input type="text" name="job_title" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition" placeholder="e.g. Manager">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Assign Role</label>
                            <select name="role" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Password</label>
                            <input type="password" name="password" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition" placeholder="••••••••" required>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-xl transition shadow-lg shadow-blue-500/30">Save User</button>
                </form>
            </div>
        @endcan

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">

            <div class="p-5 border-b border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 flex flex-col lg:flex-row justify-between items-center gap-4">
                <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300">
                    <span>Show</span>
                    <form action="{{ route('users.index') }}" method="GET">
                        <select name="per_page" onchange="this.form.submit()" class="bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-lg py-1 px-2 text-sm focus:ring-blue-500">
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    <span>entries</span>
                </div>

                <div class="flex flex-col sm:flex-row w-full lg:w-auto items-center gap-3">
                    <form action="{{ route('users.index') }}" method="GET" class="flex w-full sm:w-auto gap-2">
                        <div class="relative w-full sm:w-64">
                            <i class="fa-solid fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, user, phone..." class="pl-9 w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-lg focus:ring-1 focus:ring-blue-500 py-1.5 text-sm">
                        </div>
                    </form>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button onclick="window.print()" class="bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 py-1.5 px-4 rounded-lg transition shadow-sm text-sm"><i class="fa-solid fa-print"></i></button>
                        @can('manage-users')
                            <button @click="showAddForm = !showAddForm" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-4 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                                <i class="fa-solid fa-plus"></i> Add User
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
                        <th class="py-3 px-5">Name & Username</th>
                        <th class="py-3 px-5">Contact & Email</th>
                        <th class="py-3 px-5">Job Title</th>
                        <th class="py-3 px-5">Role</th>
                        <th class="py-3 px-5 w-24 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700/50">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-blue-50/50 dark:hover:bg-slate-700/30 transition-colors group text-sm">
                            <td class="py-3 px-5 text-center text-gray-500">{{ $users->firstItem() + $index }}</td>
                            <td class="py-3 px-5">
                                <div class="font-bold text-gray-800 dark:text-gray-200">{{ $user->name }}</div>
                                <div class="text-[11px] text-blue-500 font-medium">@ {{ $user->username }}</div>
                            </td>
                            <td class="py-3 px-5">
                                <div class="text-gray-700 dark:text-gray-300 font-medium">{{ $user->phone ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="py-3 px-5 italic text-gray-600 dark:text-gray-400">{{ $user->job_title ?? 'N/A' }}</td>
                            <td class="py-3 px-5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $user->hasRole('Super Admin') ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30' }}">
                                    {{ $user->roles->first()->name ?? 'No Role' }}
                                </span>
                            </td>
                            <td class="py-3 px-5 text-right">
                                <div class="flex justify-end gap-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                    @can('manage-users')
                                        <button @click="
                                        editData = {
                                            id: '{{ $user->id }}',
                                            name: '{{ $user->name }}',
                                            username: '{{ $user->username }}',
                                            email: '{{ $user->email }}',
                                            phone: '{{ $user->phone }}',
                                            job_title: '{{ $user->job_title }}',
                                            role: '{{ $user->roles->first()->name ?? '' }}'
                                        };
                                        showEditModal = true"
                                                class="text-blue-600 hover:text-blue-800">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    @endcan

                                    @can('delete-users')
                                        @if(auth()->id() !== $user->id && !$user->hasRole('Super Admin'))
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)" class="text-red-500"><i class="fa-solid fa-trash-can"></i></button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-gray-400">No users found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-slate-700">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        @can('manage-users')
            <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-500/75 dark:bg-slate-900/80 transition-opacity" @click="showEditModal = false"></div>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full z-50 border border-gray-100 dark:border-slate-700">
                        <form :action="`{{ url('users') }}/${editData.id}`" method="POST">
                            @csrf @method('PUT')
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between bg-gray-50 dark:bg-slate-800/50">
                                <h3 class="font-bold text-gray-900 dark:text-white">Edit User Account</h3>
                                <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                    <input type="text" name="name" x-model="editData.name" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Username</label>
                                    <input type="text" name="username" x-model="editData.username" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                    <input type="email" name="email" x-model="editData.email" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                                    <input type="text" name="phone" x-model="editData.phone" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Job Title</label>
                                    <input type="text" name="job_title" x-model="editData.job_title" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Role</label>
                                    <select name="role" x-model="editData.role" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm" required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Password (Empty to skip)</label>
                                    <input type="password" name="password" placeholder="••••••••" class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm">
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-slate-800/50 px-6 py-4 flex justify-end gap-3">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 text-gray-600 dark:text-gray-400">Cancel</button>
                                <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-xl shadow-lg hover:bg-blue-700 transition">Update User</button>
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
                text: "User access will be revoked!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Delete!',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#000000'
            }).then((result) => { if (result.isConfirmed) button.closest('form').submit(); });
        }
    </script>
</x-app-layout>
