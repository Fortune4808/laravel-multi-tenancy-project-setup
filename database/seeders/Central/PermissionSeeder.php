<?php

namespace Database\Seeders\Central;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    //Run the database seeds.
    public function run(): void
    {
        $permissions = [
            ['name' => 'manage staff', 'guard_name' => 'centralstaffs'],
            ['name' => 'manage branches', 'guard_name' => 'centralstaffs'],
            ['name' => 'manage roles', 'guard_name' => 'centralstaffs'],
            ['name' => 'manage branch staff', 'guard_name' => 'centralstaffs'],
            ['name' => 'manage branch roles', 'guard_name' => 'centralstaffs'],
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
