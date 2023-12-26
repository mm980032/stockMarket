<?php

namespace Modules\User\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\User\app\Models\User;

class UserRepository extends BaseRepository {

    public function model() {
        return User::class;
    }

    public function uniqueID()
    {
        return 'userID';
    }

    /**
     * 建立使用者
     *
     * @param array $paylaod
     * @return void
     * @author ZhiYong
     */
    public function createUser(array $paylaod){
        $this->insertData($paylaod);
    }

    /**
     * 查詢用戶(利用帳號)
     *
     * @param string $account
     * @param string $password
     * @return Model|null
     * @author ZhiYong
     */
    public function selectUserByAccount(string $account, string $password): ?Model
    {
        $model = $this->model;
        return $model
                ->select('*')
                ->where('account', $account)
                ->where('password', $password)
                ->where('isDeleted', 0)
                ->first();
    }

    public function selectUserByUserID(string $userID): ?Model
    {
        $model = $this->model;
        return $model
                ->select('*')
                ->where('userID', $userID)
                ->where('isDeleted', 0)
                ->first();
    }
}

