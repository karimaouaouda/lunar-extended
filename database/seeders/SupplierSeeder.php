<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Lunar\Models\Country;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = Country::all();
        (new SupplierFactory())->count(10)->create();
        foreach (Supplier::all() as $supplier) {
            $country = $countries->random();
            $state = $country->states->random();
            $supplier->address()->create([
                'country' => $country->id,
                'state' => $state->id,
                'address_line' => fake()->text(100),
                'latitude' => 1.2,
                'longitude' => 1.2,
                'city' => $state->name
            ]);
        }
    }
}
