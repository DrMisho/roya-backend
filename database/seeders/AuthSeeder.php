<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('oauth_clients')->insert([
            'id' => 1,
            'name' => 'Roya Personal Access Client',
            'secret' => 'ukrSQltUUGlBdyr9BJAIof7F2kLfPQrkSwJ0u9h3',
            'redirect' => 'http://localhost',
            'personal_access_client' => 1,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oauth_clients')->insert([
            'id' => 2,
            'name' => 'Roya Password Grant Client',
            'secret' => 'ZXzpNfycrghLVs5gzkrg3uBDaC2gtzqa3MdeAo9E',
            'redirect' => 'http://localhost',
            'provider' => 'users',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
