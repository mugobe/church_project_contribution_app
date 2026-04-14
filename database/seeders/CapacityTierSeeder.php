<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CapacityTier; 

class CapacityTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        CapacityTier::insert([
    ['name' => 'Platinum', 'weight' => 4, 'description' => 'Highest contribution tier', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Gold',     'weight' => 3, 'description' => 'High contribution tier',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Silver',   'weight' => 2, 'description' => 'Mid contribution tier',     'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Standard', 'weight' => 1, 'description' => 'Base contribution tier',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
]);
    }
}
