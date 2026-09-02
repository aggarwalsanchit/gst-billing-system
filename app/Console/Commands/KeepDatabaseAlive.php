<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class KeepDatabaseAlive extends Command
{
    protected $signature = 'db:keep-alive';
    protected $description = 'Keep the database connection alive';

    public function handle()
    {
        try {
            DB::select('SELECT 1');
            $this->info('✅ Database is alive at ' . now());
        } catch (\Exception $e) {
            $this->error('❌ Database error: ' . $e->getMessage());
        }
    }
}