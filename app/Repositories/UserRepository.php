<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class StockRepository.
 */
class UserRepository extends BaseRepository
{

    public function model() {
        return User::class;
    }

    public function uniqueID()
    {
        return 'userID';
    }

}
