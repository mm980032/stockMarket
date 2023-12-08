<?php

namespace App\Repositories;

use App\Models\Stock;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockRepository.
 */
class StockRepository extends BaseRepository
{

    public function model() {
        return Stock::class;
    }

    public function uniqueID()
    {
        return 'id';
    }

    // 新增或編輯股票資訊
    public function updateBaseStockInfo(array $payload){
        $this->model->upsert($payload, ['stockCode']);
    }
}
