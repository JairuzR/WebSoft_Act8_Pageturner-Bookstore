<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    // Static cache – loaded ONCE, reused for all 1M records
    protected static array $categoryIds = [];
    protected static array $publishers = [
        'Penguin Random House', 'HarperCollins', 'Simon & Schuster',
        'Hachette Book Group', 'Macmillan Publishers', 'Oxford University Press',
        'Cambridge University Press', 'Scholastic', 'Bloomsbury Publishing',
        'Pearson', 'Wiley', 'Springer Nature', 'Elsevier', 'Taylor & Francis',
        'SAGE Publishing'
    ];

    public function definition(): array
    {
        // Load category IDs only if not already cached
        if (empty(self::$categoryIds)) {
            self::$categoryIds = Category::pluck('id')->toArray();
        }

        // Pick a random format and set realistic pricing
        $format = $this->faker->randomElement(['hardcover', 'paperback', 'ebook', 'audiobook']);
        $basePrice = match ($format) {
            'hardcover' => $this->faker->randomFloat(2, 18, 45),
            'paperback' => $this->faker->randomFloat(2, 8, 22),
            'ebook'     => $this->faker->randomFloat(2, 3, 15),
            'audiobook' => $this->faker->randomFloat(2, 12, 35),
        };

        return [
            'isbn'            => $this->generateValidIsbn13(),
            'title'           => $this->faker->unique()->sentence(rand(2, 6)),
            'author'          => $this->faker->name(),
            'publisher'       => $this->faker->randomElement(self::$publishers),
            'price'           => $basePrice,
            'stock_quantity'  => $this->faker->numberBetween(0, 1000),
            'category_id'     => $this->faker->randomElement(self::$categoryIds),
            'format'          => $format,
            'is_active'       => $this->faker->boolean(85),
            'description'     => $this->faker->optional(0.7)->paragraphs(3, true),
            'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),   // 'Y-m-d H:i:s'
            'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
            'published_at' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Generate a valid ISBN-13 with correct checksum.
     */
    private function generateValidIsbn13(): string
    {
        // Start with 978 (book industry prefix)
        $digits = [9, 7, 8];
        // Next 9 digits random
        for ($i = 0; $i < 9; $i++) {
            $digits[] = rand(0, 9);
        }
        // Calculate checksum using ISBN-13 algorithm
        $sum = 0;
        foreach ($digits as $i => $d) {
            $sum += ($i % 2 === 0) ? $d : $d * 3;
        }
        $checksum = (10 - ($sum % 10)) % 10;
        $digits[] = $checksum;
        return implode('', $digits);
    }

    // Optional state: a "bestseller" with high stock
    public function bestseller(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => $this->faker->numberBetween(500, 1000),
            'is_active'      => true,
        ]);
    }
}