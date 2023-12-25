<?php

namespace Modules\Login\app\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Modules\Login\app\Http\Requests\RegisterUserRequest;
use Modules\Login\Services\LoginService;

class LoginController extends BaseController
{


    public function __construct(
        private LoginService $service
    ){}

    /**
     * 註冊帳號
     *
     * @return JsonResponse
     * @author ZhiYong
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $post = $request->validated();
        try {
            $this->service->register($post);
            return $this->returnSuccessMsg();
        } catch (\Throwable $th) {
            return $this->returnErrorMsg('失敗，原因：'. $th->getMessage());
        }
    }
}
