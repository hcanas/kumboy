<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // insert super admin account
        User::query()
            ->create([
                'name' => env('SUPERADMIN_NAME'),
                'email' => env('SUPERADMIN_EMAIL'),
                'email_verified_at' => now(),
                'password' => Hash::make(env('SUPERADMIN_PASSWORD')),
                'role' => 'superadmin',
            ]);

        $this->call([
            UserSeeder::class,
            StoreSeeder::class,
            ProductSeeder::class,
            ProductSpecificationSeeder::class,
        ]);
    }
}
