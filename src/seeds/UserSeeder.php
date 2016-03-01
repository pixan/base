<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Pixan\Base\User as User;

class UserSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        // $this->call(UserTableSeeder::class);
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@pixan.io',
            'password' => Hash::make('12345')
        ]);
        
        Model::reguard();
    }
}