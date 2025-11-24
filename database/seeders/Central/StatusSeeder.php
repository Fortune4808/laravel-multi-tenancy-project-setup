<?php

namespace Database\Seeders\Central;

use App\Models\Central\Setup\Status;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['status_name' => 'ACTIVE'],
            ['status_name' => 'INACTIVE'],
            ['status_name' => 'SUSPENDED'],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate($status);
        }
    }
}
