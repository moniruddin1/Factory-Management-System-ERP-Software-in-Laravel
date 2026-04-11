<x-guest-layout>
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/30 mb-4 shadow-inner">
            <i class="fa-solid fa-shield-halved text-3xl text-indigo-600 dark:text-indigo-400"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Security Check</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 px-4">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-gray-400"></i>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                       class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-900/50 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm sm:text-sm" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-indigo-500/30 text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
            <i class="fa-solid fa-check-circle mr-2"></i> Confirm Password
        </button>
    </form>
</x-guest-layout>
