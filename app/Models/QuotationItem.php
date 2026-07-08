<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'warehouse_id',
        'quantity',
        'unit_price',
        'subtotal'
    ];

    // Relasi balik ke header
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    // Relasi ke data master inventori
    public function inventory()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
