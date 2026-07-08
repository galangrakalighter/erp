<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    protected $table = 'requisition_items';
    protected $fillable = ['requisition_id', 'warehouse_id', 'jumlah_minta'];

    // Relasi: Kembali ke induk permintaannya
    public function requisition()
    {
        return $this->belongsTo(MaterialRequisition::class, 'requisition_id');
    }

    // Relasi: Ke master data barang
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
