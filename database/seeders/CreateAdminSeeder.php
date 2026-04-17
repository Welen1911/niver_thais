<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => app('config')->get('app.admin_email', 'admin@teste.com'),
            'password' => bcrypt(app('config')->get('app.admin_password', 'password')),
        ]);
    }
}
