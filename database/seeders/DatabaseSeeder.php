<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user only if it doesn't exist
        if (!User::where('email', 'admin@pageturner.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@pageturner.com',
                'role' => 'admin',
            ]);
        }

        // Create customer users only if we have less than 10
        if (User::where('role', 'customer')->count() < 10) {
            User::factory(10)->create(['role' => 'customer']);
        }

        // Create categories if none exist (needed before mass book seeding)
        if (Category::count() == 0) {
            Category::factory(20)->create(); // Increase to 20 categories for variety
        }

        // Now run the 1M book seeder (chunked, memory-safe)
        $this->call(MassBookSeeder::class);

        // Optionally seed reviews after mass seeding (but be careful with time)
        // You can skip reviews for now or run a separate lightweight review seeder later.
    }
}