<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockDumping extends Model
{
    protected $fillable = [
        'armada_unit_id', 'stock_area_id',
        'volume', 'user_id', 'insert_via', 'date', 'time',
        'material_type', 'seam_id', 'customer_id', 'shift'
    ];

    public function stockArea() {
        return $this->belongsTo(StockArea::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }
}
