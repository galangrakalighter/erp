<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Peminta
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->text('catatan')->nullable();
            $table->string('cabang')->nullable(); // Pending, Approved, Rejected
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('material_requisitions')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouse');
            $table->integer('jumlah_minta');
            $table->integer('harga');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requisitions');
        Schema::dropIfExists('requisition_items');
    }
};
