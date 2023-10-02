<?php

namespace App\Models;

class FocusStock extends baseModel
{
    public $table = 'FocusStock';

    public function stockInfo(){
        return $this->belongsTo(Stock::class, 'stockCode', 'stockCode');
    }
}
