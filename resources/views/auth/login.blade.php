<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
            @if(isset($company_info) && $company_info->logo)
                <img src="{{ asset('storage/' . $company_info->logo) }}" alt="Company Logo" class="h-16 w-auto object-contain drop-shadow-sm">
            @else
                <div class="h-16 w-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <i class="fa-solid fa-layer-group text-3xl text-white"></i>
                </div>
            @endif
        </div>

        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            Welcome to <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">{{ $company_info->name ?? 'Shoe ERP' }}</span>
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5 font-medium">
            {{ $company_info->subtitle ?? 'Smart Factory Management System' }}
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fa-regular fa-envelope text-gray-400"></i>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@example.com"
                       class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-900/50 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm sm:text-sm" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition" href="{{ route('password.request') }}">
                        Forgot Password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-gray-400"></i>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                       class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-900/50 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm sm:text-sm" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 dark:border-slate-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-slate-900 transition">
            <label for="remember_me" class="ms-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">
                {{ __('Keep me logged in') }}
            </label>
        </div>

        <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-blue-500/30 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:-translate-y-0.5">
            <i class="fa-solid fa-right-to-bracket mr-2"></i> Log In to Dashboard
        </button>
    </form>

    <div class="mt-8 text-center text-xs text-gray-400 dark:text-gray-500">
        &copy; {{ date('Y') }} <span class="font-semibold">{{ $company_info->name ?? 'Shoe ERP' }}</span>. All rights reserved.
    </div>
</x-guest-layout>
