<?php

namespace App\Repositories;

use App\Models\FocusStock;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockRepository.
 */
class FocusStockRepository extends BaseRepository
{
    public function model() {
        return FocusStock::class;
    }

    public function uniqueID()
    {
        return 'focusID';
    }

    // 建立關注股票
    public function createFocus(array $payload) : void
    {
        $this->model->upsert($payload, ['userID', 'stockCode']);
    }

    /**
     * 取得關注股票
     *
     * @param string $userID
     * @return Collection
     * @author ZhiYong
     */
    public function selectAllFocusStockByUserID(string $userID) : Collection
    {
        return $this->model
            ->where('remmo', $userID)
            ->get();
    }
}
