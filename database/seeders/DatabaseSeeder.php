<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user=User::factory()->create([
            'name' => 'admin admin',
            'email' => 'admin@mail.com',
            'password'=>bcrypt('admin'),
            'email_verified_at'=>now(),
        ]);
        if(!Role::where('name','admin')->exists()){
            Role::create(['name'=>'admin']);
        }
        $user->assignRole('admin');
    }
}
