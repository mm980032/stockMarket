<?php
namespace App\Services;

use App\Libraries\Dividend\Services\DividendService;
use App\Libraries\Dividend\Services\FinMindDividendService;
use App\Libraries\Dividend\Services\FinMinService;
use App\Libraries\FinMindLibrary;
use App\Libraries\Pusher\Services\LineBotService;
use App\Libraries\Pusher\Services\LineNotificationService;
use App\Repositories\FocusStockRepository;
use App\Repositories\StockRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PDO;
use PDOException;

class StockMarketService {


    // private $headers = [
    //     'Content-Type: application/json',
    //     'X-API-KEY: YjdmZTY4OTAtNTAzYS00ZGRhLTg4MjctNjFhMzk2YjE1ZWQxIDg5YzY5ZGM1LTdjYTQtNDlkMS1hMTA5LTdkNTViNDEwNjFiMQ=='
    // ];
    private $headers = [
        'Content-Type' => 'application/json',
        'X-API-KEY' => 'YjdmZTY4OTAtNTAzYS00ZGRhLTg4MjctNjFhMzk2YjE1ZWQxIDg5YzY5ZGM1LTdjYTQtNDlkMS1hMTA5LTdkNTViNDEwNjFiMQ=='
    ];

    private $focusCode = ['2356', '2884', '00878'];

    private $stockAllDayUrl;
    private $stockBwibbuUrl;
    private $fugleStockQuote;
    public function __construct(
        private LineBotService $botPush,
        private LineNotificationService $lineNotify,
        private StockRepository $stockRepo,
        private FocusStockRepository $focusStockRepo,
        private UserRepository $userRepo,
        // private DividendService $dividendServ
        private FinMindDividendService $finMindDividendServ,
        private FinMindLibrary $finMindLibrary,
    ){
        $this->stockAllDayUrl =  config('stockUrl.STOCK_DAY_ALL');
        $this->stockBwibbuUrl =  config('stockUrl.BWIBBU_ALL');
        $this->fugleStockQuote = config('stockUrl.FUGLE_QUOTE');
    }

    /**
     * LineBot發送消息
     *
     * @return JsonResponse
     * @author ZhiYong
     */
    public function notifyStockMarketInfo() : JsonResponse
    {
        // return response()->json($this->botPush->sendNotification(['laravel Test!']));
        return response()->json($this->lineNotify->sendNotification('laravel Test!'));
    }

    /**
     * 開盤低於最低價
     *
     * @return void
     * @author ZhiYong
     */
    public function compareOpeningAndLowest()
    {
        $result = curl('post', $this->stockAllDayUrl, $this->headers);
        $data = collect(json_decode($result['content'])) ;
        $notifyMsg = [];
        foreach ($this->focusCode as $key => $code) {
            $notie = $data->where('Code', $code)->first();
            if($notie->OpeningPrice > $notie->LowestPrice){
                $notifyMsg[] = "【低價通知】"."\n".
                "日期：" . date('Y-m-d H:i:s') ."\n".
                "個股名稱：". $notie->Name. "(" . $notie->Code .")"."\n".
                "開盤價〖高於〗最低價格"."\n".
                "〖開盤價格〗 $notie->OpeningPrice"."\n".
                "〖最低價格〗 $notie->LowestPrice";
            }
        }
        if(!empty($notifyMsg)){
            return response()->json($this->botPush->sendNotification($notifyMsg));
        }
    }

    /**
     * 呼叫url api
     *
     * @param string $url
     * @param string $method
     * @return SupportCollection
     * @author ZhiYong
     */
    private function fetchStockDataFromUrl(string $url, string $method = 'GET', array $data = []): SupportCollection {
        switch ($method) {
            case 'GET':
                $data = Http::withHeaders($this->headers)->get($url)->json();
                break;
            case 'POST':
                $data = Http::withHeaders($this->headers)->post($url, $data)->json();
            default:
                throw new Exception('curl type error!');
        }
        return collect($data);
    }

    /**
     * 取得關注股票資訊
     *
     * @return void
     * @author ZhiYong
     */
    public function getFocuseInfo() : void
    {
        $msg[] = "【通知】";
        $focusCode = $this->focusStockRepo->selectAllData([['userID', 'testUserID']]);
        foreach ($focusCode as $key => $each) {
            $url = sprintf($this->fugleStockQuote, $each['stockCode']);
            // call URL API
            $data = $this->fetchStockDataFromUrl($url);
            $msg[] = "日期：" . date('Y-m-d H:i:s') ."\n".
                "個股名稱：". $data['name']. "(" . $data['symbol'] .")"."\n".
                "〖平均價格〗". $data['avgPrice']."\n".
                "〖開盤價格〗". $data['openPrice']."\n".
                "〖最低價格〗". $data['lowPrice'];
        }
        $msg = implode("\n\n", $msg);
        if(!empty($msg)){
            $this->lineNotify->sendNotification($msg, 'detail');
        }
    }

    /**
     * 推薦購買
     *
     * @return void
     * @author ZhiYong
     */
    public function recommendBuy($method = 'remmo') : void {
        // 關注股票
        $focus = $this->focusStockRepo->selectAllFocusStockByUserID($method);
        // 取得所有股票代碼
        $focuCodes = $focus->pluck('stockCode');
        // 訊息組合
        $msg[0] = "當前判斷設定： 殖利率為3%以上 且 開盤價+-0.05%"."\n".
        "掃描股票範圍:";
        foreach ($focus as $key => $item) {
            $msg[0] .= $item['name'] . '(' .$item['stockCode'] . '), ';
        }

        // 推薦購買邏輯(關注股票)
        foreach ($focuCodes as $key => $code) {
            $url = sprintf($this->fugleStockQuote, $code);
            // call URL API
            $data = $this->fetchStockDataFromUrl($url);
            // 取得殖利率
            $dividendYield = $this->getDividendYieldByFinMind($data);
            // 購買期望價格
            [$expectHightPrice, $expectLostPrice] = $this->getExpectPrice($data['openPrice'], 0.005);
            if($dividendYield >= 3 && ($expectLostPrice <= $data['lastPrice'] && $data['lastPrice'] <= $expectHightPrice)){
                $msg[] = "〖日期〗" . date('Y-m-d H:i:s') ."\n".
                "〖個股名稱〗". $data['name']. "(" . $data['symbol'] .")" . "\n".
                "〖殖利率〗". "(" .number_format($dividendYield, 2) . "%)" ."\n".
                "〖開盤價格〗". $data['openPrice']."\n".
                "〖當前價格〗". $data['lastPrice'] . '('. $data['change'] .')'."\n".
                "〖期望最高價格〗". $expectHightPrice."\n".
                "〖期望最低價格〗". $expectLostPrice."\n".
                "〖最高價格〗". $data['highPrice']. '('. $data['highTime'] .')'."\n".
                "〖最低價格〗". $data['lowPrice']. '('. $data['lowTime'] .')'."\n";
            }

        }
        if(!empty($msg[0])){
            // 停用
            // $this->botPush->sendNotification($msg);
            foreach ($msg as $key => $item) {
                $this->lineNotify->sendNotification($item, $method);
            }
        }
    }
    /**
     * 取得高低期望值
     *
     * @param string $openPrice
     * @param float $ratio
     * @return array
     * @author ZhiYong
     */
    public function getExpectPrice(string $openPrice, float $ratio) : array {
        $hightPrice = $openPrice + ($openPrice * $ratio);
        $lostPrice = $openPrice - ($openPrice * $ratio);

        return [$hightPrice, $lostPrice];
    }

    /**
     * 取得殖利率
     *
     * @param \Illuminate\Support\Collection $data
     * @return float
     * @author ZhiYong
     */
    public function getDividendYieldByFinMind(\Illuminate\Support\Collection $data) : float{
        $dividends = $this->finMindDividendServ->getDividend($data['symbol']);
        if ($dividends['status'] == 200) {
            $dividends = $dividends['data'];
        }

        // 所有股利
        $all_dividend = array_column($dividends, 'CashEarningsDistribution');
        // 平均股利
        $avg_dividend = array_sum($all_dividend)/ count($dividends);
        // 殖利率 => 股利/股價
        $dividendYield = ($avg_dividend / $data['lastPrice']) * 100;
        return $dividendYield;

    }

    public function ownStockPricePush(){
        $msg = [];
        // 關注股票
        $focus = $this->focusStockRepo->selectAllFocusStockByUserID('own');
        // 取得所有股票代碼
        $focuCodes = $focus->pluck('stockCode');
        // 推薦購買邏輯(關注股票)
        foreach ($focuCodes as $key => $code) {
            $url = sprintf($this->fugleStockQuote, $code);
            // call URL API
            $data = $this->fetchStockDataFromUrl($url);
            $msg[] = "日期：" . date('Y-m-d H:i:s') ."\n".
            "個股名稱：". $data['name']. "(" . $data['symbol'] .")"."\n".
            "〖當前股價〗". $data['lastPrice']. "\n". $data['change'] ." (" . $data['changePercent'] ."%) " ."\n";
        }
        if(!empty($msg)){
            foreach ($msg as $key => $item) {
                $this->lineNotify->sendNotification($item, 'own');
            }
        }
    }
}
