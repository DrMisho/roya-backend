<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Package::create([
            'name' => 'اشتراك فصلي',
            'is_semester' => true,
            'price' => '25000',
        ]);
        Package::create([
            'name' => 'اشتراك على مادة',
            'is_semester' => false,
            'price' => '8000',
        ]);
    }
}
