<?php

namespace Database\Seeders\Branch;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    //Run the database seeds.
    public function run(): void
    {
        $permissions = [
            ['name' => 'manage staff', 'guard_name' => 'branchstaffs'],
            ['name' => 'manage roles', 'guard_name' => 'branchstaffs'],
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
