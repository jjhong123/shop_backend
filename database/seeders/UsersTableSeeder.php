<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'uuid' => 'U1091008201',
            'roles' => 'admin',
            'email' => 'qaawssedd456@gmail.com',
            'password' =>  Hash::make('0000')
        ]);

        User::create([
            'name' => 'Fish',
            'uuid' => 'U1091008202',
            'roles' => 'admin',
            'email' => 'kossokx@gmail.com',
            'password' => Hash::make('0000')
        ]);


        User::create([
            'name' => 'User',
            'uuid' => 'U1091008203',
            'roles' => 'user',
            'email' => 'qaawssedd457@gmail.com',
            'password' => Hash::make('0000')
        ]);
    }
}
