<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <i class="fa-regular fa-id-badge text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Profile') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ isEditing: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-2 p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-slate-700/50 relative">

                    <div class="absolute top-6 right-6 z-10">
                        <button @click="isEditing = !isEditing" type="button"
                                class="inline-flex items-center px-4 py-2 bg-blue-50 hover:bg-blue-100 dark:bg-slate-700 dark:hover:bg-slate-600 text-sm font-bold text-blue-700 dark:text-blue-400 rounded-lg transition-all">
                            <i class="fa-solid fa-pen-to-square mr-2" x-show="!isEditing"></i>
                            <i class="fa-solid fa-xmark text-red-500 mr-2" x-show="isEditing" style="display: none;"></i>
                            <span x-text="isEditing ? 'Cancel Edit' : 'Edit Profile'"></span>
                        </button>
                    </div>

                    <div class="max-w-2xl mt-4">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm sm:rounded-2xl border border-gray-100 dark:border-slate-700/50">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
