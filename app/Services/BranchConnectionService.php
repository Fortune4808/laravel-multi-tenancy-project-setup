<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BranchConnectionService
{
    //Connect dynamically to  a branch database.
    public static function connectToBranch(string $dbName, string $connection = 'branch'): void
    {
        DB::purge($connection);
        Config::set("database.connections.{$connection}.database", $dbName);
        DB::reconnect($connection);
        DB::setDefaultConnection($connection);
    }

    //Restore back to the central (default) database.
    public static function restoreCentral(string $connection = 'mysql'): void
    {
        DB::purge($connection);
        DB::reconnect($connection);
        DB::setDefaultConnection($connection);
    }

    //Create a new database.
    public static function createDatabase(string $dbName): void
    {
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
    }

    //Drop a database safely.
    public static function dropDatabase(string $dbName): void
    {
        DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
    }

    //Run any artisan command safely and throw an exception on failure.
    private static function runArtisanOrFail(string $command, array $params = []): void
    {
        $exitCode = Artisan::call($command, $params);

        if ($exitCode !== 0) {
            $output = Artisan::output();
            Log::error("Artisan [{$command}] failed: " . $output);
            throw new RuntimeException("Artisan command failed: {$command}");
        }
    }

    //Run branch migrations.
    public static function migrate(string $dbName): void
    {
        self::connectToBranch($dbName);
        self::runArtisanOrFail('migrate', [
            '--database' => 'branch',
            '--path' => '/database/migrations/branch',
            '--force' => true,
        ]);
    }

    //Run branch seeders.
    public static function seed(string $dbName): void
    {
        self::connectToBranch($dbName);
        self::runArtisanOrFail('db:seed', [
            '--database' => 'branch',
            '--class' => 'Database\\Seeders\\Branch\\BranchDatabaseSeeder',
            '--force' => true,
        ]);
    }

    public static function setupBranchDatabase(string $dbName): void
    {
        try {
            self::createDatabase($dbName);
            self::migrate($dbName);
            self::seed($dbName);
        } catch (\Throwable $e) {
            Log::error("Branch setup failed for [{$dbName}]: " . $e->getMessage());
            self::dropDatabase($dbName);
            throw new RuntimeException('Failed to create branch database: ' . $e->getMessage());
        } finally {
            self::restoreCentral();
        }
    }
}
