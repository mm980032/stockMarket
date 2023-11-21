<?php
namespace Modules\LineNotify\Services;

use Exception;
use Modules\LineNotify\Repositories\LineNotifyRepository;
use Modules\LineNotify\Validator\LineNotifyValidator;

class LineNotifyService {

    public function __construct(
        private LineNotifyValidator $validator,
        private LineNotifyRepository $repo
    )
    {}

    /**
     * 新增推播類型
     *
     * @param array $data
     * @return void
     * @author ZhiYong
     */
    public function createLineNotifyType(array $data){
        try {
            // 驗證參數
            $this->validator->validateCreateInfo($data);
            // 新增內容
            $this->repo->createLineNotifyType($data);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}
