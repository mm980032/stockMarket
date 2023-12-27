<?php

namespace Modules\Login\app\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Modules\Login\app\Http\Requests\LoginRequest;
use Modules\Login\app\Http\Requests\MFARequest;
use Modules\Login\app\Http\Requests\RegisterUserRequest;
use Modules\Login\Services\LoginService;
use Modules\Login\Validator\LoginValidator;

class LoginController extends BaseController
{


    public function __construct(
        private LoginService $service,
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

    /**
     * 登入帳號
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @author ZhiYong
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $post = $request->validated();
        try {
            $user = $this->service->getUser($post);
            $data = $this->service->login($user);
            return $this->returnSuccessMsg($data);
        } catch (\Throwable $th) {
            return $this->returnErrorMsg('提示', $th->getMessage());
        }
    }

    /**
     * mfa驗證
     *
     * @param MFARequest $request
     * @return JsonResponse
     * @author ZhiYong
     */
    public function mfa(MFARequest $request): JsonResponse
    {
        $post = $request->validated();
        try {
            // 取得Token
            $token = $request->bearerToken();
            // 取得用戶資訊
            $user = $this->service->getUserByToken($token);
            $this->service->mfa($user, $post['mfa']);
            return $this->returnSuccessMsg();
        } catch (\Throwable $th) {
            return $this->returnErrorMsg('提示', '失敗，原因：'. $th->getMessage());
        }
    }
}
