<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->smallInteger('id')->primary();
            $table->string('code', 20)->unique();
            $table->string('label', 50);
            $table->timestamps();
        });

        DB::table('transaction_types')->insert([
            ['id' => 1, 'code' => 'FORWARD', 'label' => 'Monthly Forward', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'code' => 'NEW_ADDED', 'label' => 'New Added Meds', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'code' => 'DONATION', 'label' => 'Donation Meds', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'code' => 'RETURN', 'label' => 'Return Meds', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'code' => 'CHARGES', 'label' => 'Charges (In-Patient)', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'code' => 'WARD_ISSUANCE', 'label' => 'Ward Issuance', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'code' => 'OTC', 'label' => 'OTC (Outpatient)', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'code' => 'DAMAGES', 'label' => 'Damaged Meds', 'created_at' => now(), 'updated_at' => now()],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_types');
    }
};
