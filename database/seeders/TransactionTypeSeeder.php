<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaction_types')->insert([
            ['id' => 1, 'code' => 'FORWARD', 'label' => 'Monthly Forward'],
            ['id' => 2, 'code' => 'RETURN', 'label' => 'Return Meds'],
            ['id' => 3, 'code' => 'DONATION', 'label' => 'Donation Meds'],
            ['id' => 4, 'code' => 'NEW_ADDED', 'label' => 'New Added Meds'],
            ['id' => 5, 'code' => 'DISPENSE', 'label' => 'Dispensed'],
        ]);
    }
}
