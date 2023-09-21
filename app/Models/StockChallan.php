<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockChallan extends Model
{
    use HasFactory;

    public function products(){
        return $this->belongsToMany(Product::class, 'product_stock_challan', 'stock_challan_id', 'product_id')->withPivot(['qty']);
    }
}
