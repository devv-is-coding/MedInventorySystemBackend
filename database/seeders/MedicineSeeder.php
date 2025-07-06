<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            'ACETATED RINGER SOLUTION 1L',
            'ACETYLCYSTEINE 600MG SACHET',
            'ACETYLCYSTEINE 600MG TABLET',
            'ACICLOVIR 800MG TABLET',
            'ACTIVATED CHARCOAL 50G SACHET',
            'ADENOSINE AMPULE',
            'ALLOPURINOL 100MG TABLET',
            'ALLOPURINOL 300MG TABLET',
            'AMIKACIN 250MG VIAL',
            'AMIKACIN 500MG VIAL',
        ];

        foreach ($medicines as $name) {
            Medicine::create([
                'name' => $name,
                'unit' => null,
                'dosage_form' => null,
                'description' => null,
            ]);
        }
    }
}
