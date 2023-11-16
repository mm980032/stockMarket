<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Services\FinMindStockService;
use Exception;
use Illuminate\Http\JsonResponse;

class FinMindController extends BaseController{

    public function __construct(
        private FinMindStockService $service
    ){}
    public function info() : JsonResponse {
        try {
            $this->service->getInfo();
            return $this->returnSuccessMsg();
        } catch (Exception $e) {
            return $this->returnErrorMsg('提示', $e->getMessage());
        }

    }
}
