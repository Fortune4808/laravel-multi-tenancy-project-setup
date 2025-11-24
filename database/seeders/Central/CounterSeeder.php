<?php

namespace Database\Seeders\Central;

use App\Models\Central\Setup\Counter;
use Illuminate\Database\Seeder;

class CounterSeeder extends Seeder
{
    //Run the database seeds.
    public function run(): void
    {
        $counters = [
            ['counter_id' => 'BRNCH', 'description' => 'Count number of branches'],
            ['counter_id' => 'STAF', 'description' => 'Count number of staff members'],
        ];
        foreach ($counters as $counter) {
            Counter::firstOrCreate($counter);
        }
    }
}
