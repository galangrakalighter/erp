<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_order';
    protected $fillable = [
        'nama_pemesan', 
        'alamat_pemesan', 
        'nama_tempat', 
        'alamat_tempat',
        'judul_cetak', 
        'isi_per_box', 
        'uang_muka', 
        'sisa_pembayaran',
        'tanggal_pesan', 
        'jumlah_ply', 
        'perporasi', 
        'jumlah_box',
        'no_film', 
        'salesman', 
        'keterangan', 
        'tipe_pemesanan', 
        'cabang', 
        'ukuran',
        'harga_per_box',
        'total_order',
        'terbilang',
    ];

    public $timestamps = false;

    /**
     * Relasi ke detail barang
     */
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'po_id');
    }
}
