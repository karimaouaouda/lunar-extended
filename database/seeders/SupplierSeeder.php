<?php

namespace Database\Seeders;

use Database\Factories\SupplierFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        (new SupplierFactory())->count(10)->create();
    }
}
