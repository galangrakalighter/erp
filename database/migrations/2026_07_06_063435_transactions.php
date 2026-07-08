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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['masuk', 'keluar']); // Masuk = Penjualan, Keluar = Pembelian
            $table->decimal('amount', 15, 2);
            $table->string('category'); // Misal: 'Pembelian Material', 'Penjualan Produk'
            $table->string('description')->nullable();
            $table->date('transaction_date');
            $table->unsignedBigInteger('reference_id')->nullable(); // ID dari requisition atau order
            $table->string('reference_type')->nullable(); // 'requisition' atau 'client_order'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
