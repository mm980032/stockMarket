<?php

namespace App\Http\Controllers;

use App\Services\StockMarketService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PDOException;

class StockController extends BaseController
{

    public function __construct(
        private StockMarketService $service
    ){}

    /**
     * 推薦購買通知
     *
     * @return JsonResponse
     * @author ZhiYong
     */
    public function recommendBuy() : JsonResponse {
        try {
            $this->service->recommendBuy();
            return $this->returnSuccessMsg();
        } catch (PDOException $e) {
            return $this->returnErrorMsg('提示', $e->getMessage());
        } catch (Exception $e) {
            return $this->returnErrorMsg('提示', $e->getMessage());
        }
    }
}
