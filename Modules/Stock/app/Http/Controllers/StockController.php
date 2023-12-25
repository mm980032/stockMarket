<?php

namespace Modules\Stock\app\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Stock\app\Http\Requests\CreateFocusStockRequest;
use Modules\Stock\Services\FocusStockService;
use Modules\Stock\Services\StockService;

class StockController extends BaseController
{

    public function __construct(
        private StockService $service,
        private FocusStockService $focusService
    ){}

    /**
     * 建立基礎資訊
     *
     * @return JsonResponse
     * @author ZhiYong
     */
    public function buildBasedInformation() : JsonResponse{
        try {
            $this->service->buildStockBasedInformation();
            return $this->returnSuccessMsg();
        } catch (\Throwable $th) {
            return $this->returnErrorMsg('失敗，原因：'. $th->getMessage());
        }
    }

    /**
     * 建立關注股票
     *
     * @param CreateFocusStockRequest $request
     * @return JsonResponse
     * @author ZhiYong
     */
    public function createFocuseStock(CreateFocusStockRequest $request) : JsonResponse{
        $post = $request->validated();
        try {
            $this->focusService->createFocusStock($post);
            return $this->returnSuccessMsg();
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
        } catch (Exception $e) {
            return $this->returnErrorMsg('提示', $e->getMessage());
        }
    }

    /**
     * 關注自己購買推播
     *
     * @return JsonResponse
     * @author ZhiYong
     */
    public function ownBuy() : JsonResponse {
        try {
            $this->service->ownBuy();
            return $this->returnSuccessMsg();
        } catch (Exception $e) {
            return $this->returnErrorMsg('提示', $e->getMessage());
        }
    }
}
