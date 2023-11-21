<?php

namespace Modules\LineNotify\app\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\LineNotify\Services\LineNotifyService;

class LineNotifyController extends BaseController
{
    public function __construct(
        private LineNotifyService $service
    ) {}

    /**
     * 新增line通知類型
     *
     * @param array $data
     * @return void
     * @author ZhiYong
     */
    public function createLineNotifyType(Request $request)
    {
        $post = $request->all();
        try {
            $this->service->createLineNotifyType($post);
            return $this->returnSuccessMsg([], "成功新增");
        } catch (Exception $e) {
           return $this->returnErrorMsg('提示', $e->getMessage());
        }
    }
}
