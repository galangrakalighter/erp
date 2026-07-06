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
        Schema::create('purchase_order', function (Blueprint $table){
            $table->id();
            $table->string('nama_pemesan');
            $table->text('alamat_pemesan');
            $table->string('nama_tempat');
            $table->text('alamat_tempat');
            $table->string('judul_cetak');
            $table->string('isi_per_box');
            $table->integer('uang_muka');
            $table->integer('sisa_pembayaran');
            $table->date('tanggal_pesan');
            $table->integer('jumlah_ply');
            $table->string('perporasi');
            $table->integer('jumlah_box');
            $table->string('no_film');
            $table->string('salesman');
            $table->text('keterangan');
            $table->string('tipe_pemesanan');
            $table->string('cabang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
