<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Sequence;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'ryojuniyantara@gmail.com',
            'role' => 'admin',
            'password' => '123balinux',
        ]);

        User::factory()->create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'role' => 'customer',
            'password' => '123balinux',
        ]);

        $user = User::factory(10)->create();
        $listing = Listing::factory(10)->create();

        Transaction::factory(10)
            ->state(
                new Sequence(
                    fn(Sequence $sequence) => [
                        'user_id' => $user->random()->id,
                        'listing_id' => $listing->random()->id,
                    ],
                )
            )->create();
    }
}
