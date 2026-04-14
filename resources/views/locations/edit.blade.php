<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#1e293b] rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Location: <span class="text-indigo-600 dark:text-indigo-400">{{ $location->name }}</span></h2>
                </div>

                <form action="{{ route('locations.update', $location->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Location Name</label>
                        <input type="text" name="name" value="{{ old('name', $location->name) }}" required class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg p-2.5 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 mb-2">Location Type</label>
                        <select name="type" required class="w-full bg-gray-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-600 rounded-lg p-2.5 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="raw_material_store" {{ $location->type == 'raw_material_store' ? 'selected' : '' }}>Raw Material Store (Main Godown)</option>
                            <option value="production_floor" {{ $location->type == 'production_floor' ? 'selected' : '' }}>Production Floor (WIP)</option>
                            <option value="finished_good_store" {{ $location->type == 'finished_good_store' ? 'selected' : '' }}>Finished Goods Store</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $location->is_active ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm font-bold text-gray-700 dark:text-slate-300">Active</span>
                        </label>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-bold shadow-sm transition">Update Location</button>
                        <a href="{{ route('locations.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-white px-5 py-2.5 rounded-lg font-bold transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
