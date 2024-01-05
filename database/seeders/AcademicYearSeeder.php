<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicYear::create([
            'name' => 'السنة الأولى',
        ]);
        AcademicYear::create([
            'name' => 'السنة الثانية',
        ]);
        AcademicYear::create([
            'name' => 'السنة الثالثة',
        ]);
        AcademicYear::create([
            'name' => 'السنة الرابعة',
        ]);
    }
}
