<x-guest-layout>
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/30 mb-4 shadow-inner">
            <i class="fa-regular fa-envelope-open text-3xl text-amber-600 dark:text-amber-400"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Verify Your Email</h2>
    </div>

    <div class="mb-6 text-sm text-gray-600 dark:text-gray-400 text-center leading-relaxed">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800 flex items-start">
            <i class="fa-solid fa-circle-check text-green-500 mt-0.5 mr-3"></i>
            <div class="font-medium text-sm text-green-700 dark:text-green-400">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        </div>
    @endif

    <div class="mt-6 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4 items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="w-full sm:w-auto flex justify-center items-center py-2.5 px-5 border border-transparent rounded-xl shadow-lg shadow-amber-500/30 text-sm font-bold text-white bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all transform hover:-translate-y-0.5">
                <i class="fa-regular fa-paper-plane mr-2"></i> Resend Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="w-full sm:w-auto text-sm font-semibold text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition flex justify-center items-center py-2.5 px-4 bg-gray-100 hover:bg-gray-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl border border-transparent">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
