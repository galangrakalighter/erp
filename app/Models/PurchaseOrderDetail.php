<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PurchaseOrderDetail extends Model
{
    protected $table = 'purchase_order_detail';
    protected $fillable = ['po_id', 'barang_id', 'jumlah_beli'];

    /**
     * Relasi ke barang (tabel warehouse)
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'barang_id');
    }

    /**
     * Relasi ke induk PO
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }
}
