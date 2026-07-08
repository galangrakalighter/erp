<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'type',             // 'masuk' atau 'keluar'
        'amount',           // nominal uang
        'category',         // 'Pembelian Material', 'Penjualan Produk', 'Lain-lain'
        'description',      // keterangan detail
        'transaction_date', // tanggal transaksi
        'reference_id',     // ID dari Requisition atau PO
        'reference_type'    // 'requisition' atau 'client_order'
    ];

    // Mengatur tipe data agar Laravel otomatis menghitung angka
    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function reference()
    {
        if ($this->reference_type === 'requisition') {
            return $this->belongsTo(MaterialRequisition::class, 'reference_id');
        }
        return null;
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(MaterialRequisition::class, 'reference_id', 'id');
    }
}
