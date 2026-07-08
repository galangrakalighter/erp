<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'quotations';
    protected $fillable = [
        'quotation_number',
        'nama_customer',
        'valid_until',
        'total_amount',
        'terms_and_conditions',
        'cabang',
        'status',
        'approved_by',
        'approved_at'
    ];

    // Relasi: Satu quotation memiliki banyak item
    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }
}
