<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $table = 'warehouse';

    protected $fillable = [
        'barang',
        'jumlah',
        'tipe',
        'harga',
        'satuan',
        'cabang'
    ];

    public function requisitionItems()
    {
        return $this->hasMany(RequisitionItem::class, 'warehouse_id');
    }
}
