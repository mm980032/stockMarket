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

    // 基礎股票資訊
    public function create(array $payload){
        $this->model->upsert($payload, ['stockCode', 'name']);
    }
}
