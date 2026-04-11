<section>
    <header class="mb-6 border-b border-gray-100 dark:border-slate-700 pb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Your personal account details.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('patch')

        <div class="flex items-center space-x-6 mb-8">
            <div class="shrink-0">
                @if ($user->picture)
                    <img class="h-24 w-24 object-cover rounded-full border-4 border-gray-200 dark:border-slate-600 shadow-sm" src="{{ asset('storage/' . $user->picture) }}" alt="Profile photo" />
                @else
                    <div class="h-24 w-24 rounded-full border-4 border-gray-200 dark:border-slate-600 shadow-sm bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
                        <i class="fa-solid fa-user text-3xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                @endif
            </div>
            <div class="flex-1" x-show="isEditing" style="display: none;" x-transition>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Change Profile Photo</label>
                <input type="file" name="picture" accept="image/*" class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-slate-700 dark:file:text-blue-400 hover:file:bg-blue-100 dark:hover:file:bg-slate-600 cursor-pointer transition"/>
                <x-input-error class="mt-2" :messages="$errors->get('picture')" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <input id="name" name="name" type="text"
                       class="mt-1 block w-full rounded-md shadow-sm sm:text-sm transition-colors duration-200
                           bg-white dark:bg-slate-900 border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500
                           read-only:bg-gray-50 dark:read-only:bg-slate-800/50 read-only:text-gray-600 dark:read-only:text-gray-400 read-only:border-transparent dark:read-only:border-transparent read-only:shadow-none focus:read-only:ring-0"
                       :readonly="!isEditing" value="{{ old('name', $user->name) }}" required />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="username" :value="__('Username (Unchangeable)')" />
                <input id="username" name="username" type="text"
                       class="mt-1 block w-full rounded-md sm:text-sm bg-gray-100 dark:bg-slate-800 border-gray-200 dark:border-slate-700 text-gray-500 dark:text-gray-500 cursor-not-allowed shadow-none focus:ring-0"
                       readonly value="{{ old('username', $user->username) }}" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email Address (Unchangeable)')" />
                <input id="email" name="email" type="email"
                       class="mt-1 block w-full rounded-md sm:text-sm bg-gray-100 dark:bg-slate-800 border-gray-200 dark:border-slate-700 text-gray-500 dark:text-gray-500 cursor-not-allowed shadow-none focus:ring-0"
                       readonly value="{{ old('email', $user->email) }}" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Phone Number')" />
                <input id="phone" name="phone" type="text"
                       class="mt-1 block w-full rounded-md shadow-sm sm:text-sm transition-colors duration-200
                           bg-white dark:bg-slate-900 border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500
                           read-only:bg-gray-50 dark:read-only:bg-slate-800/50 read-only:text-gray-600 dark:read-only:text-gray-400 read-only:border-transparent dark:read-only:border-transparent read-only:shadow-none focus:read-only:ring-0"
                       :readonly="!isEditing" value="{{ old('phone', $user->phone) }}" />
            </div>

            <div>
                <x-input-label for="job_title" :value="__('Job Title')" />
                <input id="job_title" name="job_title" type="text"
                       class="mt-1 block w-full rounded-md shadow-sm sm:text-sm transition-colors duration-200
                           bg-white dark:bg-slate-900 border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500
                           read-only:bg-gray-50 dark:read-only:bg-slate-800/50 read-only:text-gray-600 dark:read-only:text-gray-400 read-only:border-transparent dark:read-only:border-transparent read-only:shadow-none focus:read-only:ring-0"
                       :readonly="!isEditing" value="{{ old('job_title', $user->job_title) }}" />
            </div>

            <div>
                <x-input-label for="birthday" :value="__('Birthday')" />
                <input id="birthday" name="birthday" type="date"
                       class="mt-1 block w-full rounded-md shadow-sm sm:text-sm transition-colors duration-200
                           bg-white dark:bg-slate-900 border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500
                           read-only:bg-gray-50 dark:read-only:bg-slate-800/50 read-only:text-gray-600 dark:read-only:text-gray-400 read-only:border-transparent dark:read-only:border-transparent read-only:shadow-none focus:read-only:ring-0"
                       :readonly="!isEditing" value="{{ old('birthday', $user->birthday) }}" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="address" :value="__('Full Address')" />
                <textarea id="address" name="address" rows="3"
                          class="mt-1 block w-full rounded-md shadow-sm sm:text-sm transition-colors duration-200
                           bg-white dark:bg-slate-900 border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-blue-500
                           read-only:bg-gray-50 dark:read-only:bg-slate-800/50 read-only:text-gray-600 dark:read-only:text-gray-400 read-only:border-transparent dark:read-only:border-transparent read-only:shadow-none focus:read-only:ring-0 resize-none"
                          :readonly="!isEditing">{{ old('address', $user->address) }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 mt-6 border-t border-gray-100 dark:border-slate-700" x-show="isEditing" style="display: none;" x-transition>
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-800 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition shadow-lg shadow-blue-500/30">
                <i class="fa-solid fa-floppy-disk mr-2"></i> {{ __('Save Changes') }}
            </button>
        </div>

        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-medium text-green-600 dark:text-green-400 mt-4">
                <i class="fa-solid fa-check-circle mr-1"></i> {{ __('Profile updated successfully.') }}
            </p>
        @endif
    </form>
</section>
