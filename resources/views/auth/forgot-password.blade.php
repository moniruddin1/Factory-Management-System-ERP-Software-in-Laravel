<x-guest-layout>
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4 shadow-inner">
            <i class="fa-solid fa-unlock-keyhole text-3xl text-blue-600 dark:text-blue-400"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Forgot Password?</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 px-2">
            No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fa-regular fa-envelope text-gray-400"></i>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your registered email"
                       class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-slate-900/50 border border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm sm:text-sm" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg shadow-blue-500/30 text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:-translate-y-0.5">
            <i class="fa-regular fa-paper-plane mr-2"></i> Send Reset Link
        </button>

        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition inline-flex items-center">
                <i class="fa-solid fa-arrow-left-long mr-2"></i> Back to Login
            </a>
        </div>
    </form>
</x-guest-layout>
