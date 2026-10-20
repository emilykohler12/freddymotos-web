<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActivityLogsTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_logs_table_is_gone_after_migrating(): void
    {
        $this->assertFalse(Schema::hasTable('activity_logs'));
    }

    public function test_rolling_back_the_migration_recreates_the_empty_table(): void
    {
        Artisan::call('migrate:rollback', ['--path' => 'database/migrations/2026_10_20_000001_drop_activity_logs_table.php']);

        $this->assertTrue(Schema::hasTable('activity_logs'));
        $this->assertTrue(Schema::hasColumns('activity_logs', ['user_id', 'type', 'description', 'read_at']));
    }
}
