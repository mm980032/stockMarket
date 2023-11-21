<?php
namespace Modules\LineNotify\Validator;

use Exception;

class LineNotifyValidator {

    /**
     * 驗證輸入
     *
     * @param array $input
     * @return void
     * @author ZhiYong
     */
    public function validateCreateInfo(array $input) : void{
        $errors = '';
        // Token 不為空
        if(!isset($input["token"]) || empty($input["token"])){
            $errors .= 'token有誤！';
        }
        // Token 不為空
        if(!isset($input["name"]) || empty($input["name"])){
            $errors .= 'name有誤！';
        }
        throw_if(!empty($errors), Exception::class, $errors);
    }
}

