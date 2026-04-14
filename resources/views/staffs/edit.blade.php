<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Staff: <span class="text-blue-600 dark:text-blue-400">{{ $staff->name }}</span></h2>
                </div>

                <form action="{{ route('staffs.update', $staff->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Name</label>
                            <input type="text" name="name" value="{{ old('name', $staff->name) }}" required class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg p-2.5 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" required class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg p-2.5 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                            @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Designation</label>
                            <select name="designation" required class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg p-2.5 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="Supervisor" {{ $staff->designation == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                <option value="Line Worker" {{ $staff->designation == 'Line Worker' ? 'selected' : '' }}>Line Worker</option>
                                <option value="Manager" {{ $staff->designation == 'Manager' ? 'selected' : '' }}>Manager</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Base Salary (Optional)</label>
                            <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary', $staff->base_salary) }}" class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg p-2.5 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $staff->is_active ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm font-bold text-gray-700 dark:text-slate-300">Active</span>
                        </label>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-bold shadow-sm transition">Update Staff</button>
                        <a href="{{ route('staffs.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-white px-5 py-2.5 rounded-lg font-bold transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
