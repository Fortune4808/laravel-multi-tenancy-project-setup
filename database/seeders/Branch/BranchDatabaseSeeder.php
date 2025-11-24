<?php

namespace Database\Seeders\Branch;

use Illuminate\Database\Seeder;
use Database\Seeders\Branch\CounterSeeder;
use Database\Seeders\Central\StatusSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BranchDatabaseSeeder extends Seeder
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
