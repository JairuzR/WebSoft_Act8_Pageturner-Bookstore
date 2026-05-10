<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding reviews...');

        $users = User::where('role', 'customer')->get();

        if ($users->isEmpty()) {
            $this->command->error('No customer users found. Make sure users are seeded first.');
            return;
        }

        // Pick 20 random books to have reviews
        $books = Book::inRandomOrder()->limit(20)->get();

        foreach ($books as $book) {
            // Each book gets 3-8 reviews from random customers
            $reviewers = $users->random(min(rand(3, 8), $users->count()));

            foreach ($reviewers as $user) {
                Review::create([
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'rating'  => rand(1, 5),
                    'comment' => fake()->paragraph(),
                ]);
            }

            $this->command->info("Added reviews for: {$book->title}");
        }

        $this->command->info('Review seeding complete!');
    }
}