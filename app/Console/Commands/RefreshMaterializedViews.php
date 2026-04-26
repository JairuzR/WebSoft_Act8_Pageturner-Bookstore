<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshMaterializedViews extends Command
{
    protected $signature = 'app:refresh-materialized-views';
    protected $description = 'Refresh the materialized view for bestseller statistics';

    public function handle()
    {
        $this->info('Refreshing materialized view...');

        DB::statement('TRUNCATE TABLE mv_bestseller_stats');

        DB::statement("INSERT INTO mv_bestseller_stats
            SELECT
                category_id,
                COUNT(*) as total_books,
                AVG(price) as avg_price,
                SUM(stock_quantity) as total_inventory,
                COUNT(CASE WHEN stock_quantity > 500 THEN 1 END) as bestseller_count,
                MAX(published_at) as latest_publication
            FROM books
            WHERE is_active = true
            GROUP BY category_id
        ");

        $this->info('Materialized view refreshed.');
        return Command::SUCCESS;
    }
}