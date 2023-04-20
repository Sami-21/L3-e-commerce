<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'firstname' => 'Administrator',
            'lastname' => 'Administrator',
            'email' => 'admin@gmail.com ',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ]);
        DB::table('users')->insert([
            'firstname' => 'sami',
            'lastname' => 'maachi',
            'email' => 'sami@gmail.com ',
            'password' => bcrypt('123456'),
            'role_id' => 2,
        ]);
    }
}
