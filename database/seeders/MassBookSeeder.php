<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MassBookSeeder extends Seeder
{
    private const CHUNK_SIZE = 500;          // smaller chunk for lower peak memory
    private const TOTAL_RECORDS = 20;   // Originally 1000000, using only 20 for smaller dataset

    public function run(): void
    {
        // Disable query log and event dispatcher to save memory
        DB::connection()->disableQueryLog();
        DB::unsetEventDispatcher();

        $this->command->info('Starting mass book seeding...');
        $this->command->info('Target: ' . number_format(self::TOTAL_RECORDS) . ' records');
        $startTime = microtime(true);

        $categories = DB::table('categories')->pluck('id')->toArray();
        $publishers = [
            'Penguin Random House', 'HarperCollins', 'Simon & Schuster',
            'Hachette Book Group', 'Macmillan Publishers', 'Oxford University Press',
            'Cambridge University Press', 'Scholastic', 'Bloomsbury Publishing',
            'Pearson', 'Wiley', 'Springer Nature', 'Elsevier', 'Taylor & Francis',
            'SAGE Publishing'
        ];
        $formats = ['hardcover', 'paperback', 'ebook', 'audiobook'];

        $inserted = 0;
        while ($inserted < self::TOTAL_RECORDS) {
            $batchSize = min(self::CHUNK_SIZE, self::TOTAL_RECORDS - $inserted);
            $records = [];

            for ($i = 0; $i < $batchSize; $i++) {
                $format = $formats[array_rand($formats)];
                $records[] = [
                    'title'          => 'Book ' . ($inserted + $i + 1),
                    'author'         => 'Author ' . ($inserted + $i + 1),
                    'isbn' => '978' . str_pad($inserted + $i, 9, '0', STR_PAD_LEFT) . rand(0, 9),
                    'publisher'      => $publishers[array_rand($publishers)],
                    'price'          => rand(10, 5000) / 100,
                    'stock_quantity' => rand(0, 1000),
                    'category_id'    => $categories[array_rand($categories)],
                    'format'         => $format,
                    'is_active'      => (bool)rand(0, 1),
                    'published_at'   => Carbon::now()->subDays(rand(0, 1825))->toDateTimeString(),
                    'description'    => 'Desc ' . ($inserted + $i + 1),
                    'created_at'     => Carbon::now()->toDateTimeString(),
                    'updated_at'     => Carbon::now()->toDateTimeString(),
                ];
            }

            DB::table('books')->insert($records);

            $inserted += $batchSize;
            $progress = round(($inserted / self::TOTAL_RECORDS) * 100, 2);
            $this->command->info("Inserted {$inserted} / " . number_format(self::TOTAL_RECORDS) . " ({$progress}%)");

            // Free memory after each chunk
            unset($records);
            gc_collect_cycles();
        }

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);
        $this->command->info("Seeding complete in {$duration} seconds.");
    }
}