<?php
namespace App\Libraries\Dividend\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class DividendService {

    private $headers = [
        "Accept: application/json"
    ];

    /**
     * 取得上市公司股利分派情形
     *
     * @return Collection
     * @author ZhiYong
     */
    public function getDividend() : Collection {
        // 台灣證交所(上市公司股利分派情形-董事會通過)
        $data = Http::withHeaders($this->headers)->get("https://openapi.twse.com.tw/v1/opendata/t187ap39_L")->json();
        return collect($data);

    }
}

