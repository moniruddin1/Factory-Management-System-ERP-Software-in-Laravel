<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyInfo;

class CompanyInfoSeeder extends Seeder
{
    public function run(): void
    {
        CompanyInfo::create([
            'name' => 'Shoe ERP',
            'subtitle' => 'Smart Factory Management',
            'address' => 'Dhaka, Bangladesh',
            'phone' => '+880 1234 567890',
            'email' => 'info@shoe-erp.com',
            'website' => 'www.shoe-erp.com',
        ]);
    }
}
