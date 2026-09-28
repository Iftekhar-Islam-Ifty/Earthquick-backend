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
            VendorSeeder::class,
            EarthquickSeeder::class,
            NousTelosBagSeeder::class,
        ]);

        // A bootstrap admin is opt-in for local/test environments only. Never
        // reset an existing account's password or elevate an existing user.
        if (app()->environment(['local', 'testing'])) {
            $email = env('EARTHQUICK_SEED_ADMIN_EMAIL');
            $password = env('EARTHQUICK_SEED_ADMIN_PASSWORD');

            if (filled($email) && filled($password) && ! User::where('email', $email)->exists()) {
                User::create([
                    'name' => 'Earthquick Admin',
                    'email' => $email,
                    'phone' => '01700000000',
                    'password' => Hash::make($password),
                    'is_admin' => true,
                    'city' => 'Chattogram',
                ]);
            }
        }
    }
}
