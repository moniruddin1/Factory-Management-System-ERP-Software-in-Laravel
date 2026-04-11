<x-guest-layout>
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4 shadow-inner">
            <i class="fa-solid fa-user-plus text-3xl text-blue-600 dark:text-blue-400"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create an Account</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 px-2">
            Join <span class="font-semibold text-blue-600">{{ $company_info->name ?? 'Shoe ERP' }}</span> to get started
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fa-regular fa-user text-gray-400"></i>
                </div>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe"
                       class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-900/50 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm sm:text-sm" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fa-regular fa-envelope text-gray-400"></i>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="admin@example.com"
                       class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-900/50 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm sm:text-sm" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-gray-400"></i>
                </div>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••"
                       class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-900/50 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm sm:text-sm" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Confirm Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock-alert text-gray-400"></i>
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••"
                       class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-900/50 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm sm:text-sm" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full flex justify-center items-center py-3 px-4 mt-2 border border-transparent rounded-xl shadow-lg shadow-blue-500/30 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:-translate-y-0.5">
            <i class="fa-solid fa-user-check mr-2"></i> Register Account
        </button>

        <div class="text-center mt-6 pt-4 border-t border-gray-100 dark:border-slate-700/50">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Already registered?
                <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition">
                    Log in here
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
