<?php
namespace Pixan\Base;

use Illuminate\Database\Seeder;

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