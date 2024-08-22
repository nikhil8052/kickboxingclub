<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {

        DB::table('permissions')->delete();
        
        $permissions = [
            'memberships',
            'payrolls',
            'orders',
            'members',
            'billing',
            'employees'
        ];

        // Insert the permissions into the database
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'permission' => $permission,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
