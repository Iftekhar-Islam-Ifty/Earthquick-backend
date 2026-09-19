<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EarthquickSeeder::class,
            VendorSeeder::class,
        ]);

        // Seed or update master Earthquick Admin user
        User::updateOrCreate(
            ['email' => 'admin@earthquick.com'],
            [
                'name' => 'Earthquick Admin',
                'phone' => '01700000000',
                'password' => Hash::make('password123'),
                'is_admin' => true,
                'city' => 'Chattogram',
            ]
        );
    }
}
