<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'username' => 'mustafa',
            'phone_number' => '0935878539',
            'password' => Hash::make('Mm#123123'),
        ]);

        $superAdmin = Role::create([
            'name' => 'super-admin'
        ]);

        $user->assignRole($superAdmin);


        $student = Role::create([
            'name' => 'student'
        ]);

        $user = User::create([
            'username' => 'aghyad',
            'phone_number' => '0960686912',
            'password' => Hash::make('Mm#123123'),
        ]);

        $user->assignRole($student);
    }
}
