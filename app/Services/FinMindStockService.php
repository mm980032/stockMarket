<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FinMindStockService{

    private $headers = [
        "Accept: application/json",
    ];

    public function __construct(){}

    public function getInfo(){
        // $result = curl('GET', 'https://api.finmindtrade.com/api/v3/data?dataset="StockDividend"?stock_id="00878"?date="2023-01-01"', $this->headers);
        // var_dump(collect(json_decode($result['content'])));exit;
        // $url = "https://api.finmindtrade.com/api/v3/data";
        // $parameters = [
        //     "dataset" => "StockDividend",
        //     "stock_id" => "2884",
        //     "date" => "2023-01-01",
        // ];

        // $response = Http::get($url, $parameters);
        // $data = $response->json();
        // var_dump($data);   exit;

        // return collect(json_decode($result['content']));
    }
}
