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
     * 建立關注股票
     *
     * @param Request $request
     * @return JsonResponse
     * @author ZhiYong
     */
    public function createFocusStock(Request $request) : JsonResponse {
        $post = $request->all();
        try {
            $this->service->createFocusStock($post);
            return $this->returnSuccessMsg();
        } catch (PDOException $e) {
            return $this->returnErrorMsg('提示', $e->getMessage());
        } catch (Exception $e) {
            return $this->returnErrorMsg('提示', $e->getMessage());
        }
    }

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

    /**
     * 建制股票基本資訊
     *
     * @return JsonResponse
     * @author ZhiYong
     */
    public function buildBase() : JsonResponse{
        try {
            $this->service->buildBase();
            return $this->returnSuccessMsg();
        } catch (PDOException $e) {
            return $this->returnErrorMsg('提示', $e->getMessage());
        } catch (Exception $e) {
            return $this->returnErrorMsg('提示', $e->getMessage());
        }
    }
}
