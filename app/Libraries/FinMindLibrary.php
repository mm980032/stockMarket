<?php
namespace App\Libraries;

use Illuminate\Support\Facades\Http;

class FinMindLibrary{

    public function getStocks(){
        $url = "https://api.finmindtrade.com/api/v3/data";
        $parameters = [
            "dataset" => "TaiwanStockInfo"
        ];

        $response = Http::get($url, $parameters);
        $data = $response->json();
        return $data;
    }
}
