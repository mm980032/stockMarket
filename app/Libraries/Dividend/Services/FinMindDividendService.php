<?php
namespace App\Libraries\Dividend\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class FinMindDividendService {

    private $headers = [
        "Accept: application/json"
    ];

    /**
     * 取得上市公司股利分派情形
     *
     * @return array
     * @author ZhiYong
     */
    public function getDividend(string $code) : array {
        $fiveYearsAgo = date('Y-01-01', strtotime('-5 years', time()));
        $url = "https://api.finmindtrade.com/api/v3/data";
        $parameters = [
            "dataset" => "StockDividend",
            "stock_id" => $code,
            "date" => $fiveYearsAgo,
        ];

        $response = Http::get($url, $parameters);
        $data = $response->json();
        return $data;
    }
}

