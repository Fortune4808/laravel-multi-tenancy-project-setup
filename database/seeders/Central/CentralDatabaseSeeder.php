<?php

namespace Database\Seeders\Central;

use Illuminate\Database\Seeder;
use Database\Seeders\Central\CounterSeeder;
use Database\Seeders\Central\StatusSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CentralDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            StatusSeeder::class,
            CounterSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
