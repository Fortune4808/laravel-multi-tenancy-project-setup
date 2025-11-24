<?php

namespace Database\Seeders\Branch;

use Illuminate\Database\Seeder;
use App\Models\Branch\Setup\Counter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CounterSeeder extends Seeder
{
    //Run the database seeds.
    public function run(): void
    {
         $counters = [
            ['counter_id' => 'USER', 'description' => 'Count number of users'],
            ['counter_id' => 'STAF', 'description' => 'Count number of staff members'],
        ];
        foreach ($counters as $counter) {
            Counter::firstOrcreate($counter);
        }
    }
}
