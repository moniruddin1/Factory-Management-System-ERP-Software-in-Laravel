<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\CompanyInfo;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ডাটাবেজে টেবিলটি আছে কিনা তা চেক করে নিচ্ছি,
        // যাতে ফ্রেশ মাইগ্রেশনের সময় কোনো এরর না আসে।
        if (Schema::hasTable('company_infos')) {
            // প্রথম রো-টি তুলে আনছি
            $company_info = CompanyInfo::first();

            // সব ভিউ ফাইলের সাথে ডাটাটি শেয়ার করে দিচ্ছি
            View::share('company_info', $company_info);
        }
    }
}
