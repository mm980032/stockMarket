<?php
namespace App\Services;

use App\Libraries\Pusher\Services\LineBotService;
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

    // private $stockAllDayUrl = 'https://mis.twse.com.tw/stock/api/getStockInfo.jsp?json=1&delay=0&ex_ch=';

    private $headers = [
        'Content-Type: application/json',
        // 'Content-Type: text/html;charset=UTF-8',
        // 'X-API-KEY: ZWFhNGRjY2YtYzA2ZS00M2VmLTk3N2MtNzE4MmI3YWYyZTJlIDViMWQ5NWE2LTMzN2QtNDk0Yy04YjdmLTkxMWQ4OTgzZTA3Zg=='
        'X-API-KEY: YTZlYmM0NjktNDFkNy00ZTI4LTkyMmEtYTk2NDJlZGVhMWU4IDcxN2I4Y2ZmLWZiY2MtNGE1NC04MzU3LTFhYmMxNTA0YzlmOQ=='
    ];

    private $focusCode = ['2356', '2884', '00878'];

    private $stockAllDayUrl;
    private $stockBwibbuUrl;
    private $fugleStockQuote;
    public function __construct(
        private LineBotService $botPush,
        private StockRepository $stockRepo,
        private FocusStockRepository $focusStockRepo,
        private UserRepository $userRepo
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
        return response()->json($this->botPush->sendNotification(['laravel Test!']));
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

    private function fetchStockDataFromUrl(string $url, string $method = 'GET'): SupportCollection {
        $result = curl($method, $url, $this->headers);
        return collect(json_decode($result['content']));
    }

    /**
     * 取得關注股票資訊
     *
     * @return JsonResponse
     * @author ZhiYong
     */
    public function getFocuseInfo() : JsonResponse
    {
        $this->updateBaseStockInfo();

        $focusCode = $this->focusStockRepo->selectAllData([['userID', 'testUserID']]);
        foreach ($focusCode as $key => $item) {
            $msg[] = "【通知】"."\n".
            "日期：" . date('Y-m-d H:i:s') ."\n".
            "個股名稱：". $item->name. "(" . $item->stockCode .")"."\n".
            "〖開盤價格〗". $item->stockInfo->openingPrice."\n".
            "〖最低價格〗". $item->stockInfo->lowestPrice;
        }
        if(!empty($msg)){
            return response()->json($this->botPush->sendNotification($msg));
        }
    }

    /**
     * 基礎資料建置
     *
     * @return void
     * @author ZhiYong
     */
    public function updateBaseStockInfo() : void {
        $create = [];
        $allStock = $this->fetchStockDataFromUrl($this->stockAllDayUrl);
        $allStockRatio = $this->fetchStockDataFromUrl($this->stockBwibbuUrl);
        foreach ($allStock as $key => $item) {
            $create[] = [
                'stockCode'     => $item->Code,
                'name'          => $item->Name,
                'openingPrice'  => $item->OpeningPrice,
                'highestPrice'  => $item->HighestPrice,
                'lowestPrice'   => $item->LowestPrice,
                'closingPrice'  => $item->ClosingPrice,
                'change'        => $item->Change,
                'peratio'       => $allStockRatio->where('Code', $item->Code)->isNotEmpty() ? $allStockRatio->where('Code', $item->Code)->first()->PEratio : '',
                'DividendYield' => $allStockRatio->where('Code', $item->Code)->isNotEmpty() ? $allStockRatio->where('Code', $item->Code)->first()->DividendYield : '',
                'pbratio'       => $allStockRatio->where('Code', $item->Code)->isNotEmpty() ? $allStockRatio->where('Code', $item->Code)->first()->PBratio : ''
            ];
        }
        $this->stockRepo->updateBaseStockInfo($create);
        $this->botPush->sendNotification(["目前時間:" . date('Y-m-d H:i:s') . "\n" ."已更新股票資訊，共：" . count($create)."筆"]);
    }

    /**
     * 建立關注
     *
     * @param array $post
     * @return void
     * @author ZhiYong
     */
    public function createFocusStock(array $post) : void
    {
        DB::beginTransaction();
        try {
            // 取得輸入資訊
            $stocks = $this->stockRepo->selectAllData([], ['stockCode', 'name'],
                function ($query) use($post){
                    return $query->whereIn('stockCode', $post['stockCode']);
                }
            );
            // 當前關注資訊
            $focus = $this->focusStockRepo->selectAllData([['userID', $post['userID']]]);
            $this->createFocus($focus, $stocks, $post);
            $this->deleteFocus($focus, $post);
            DB::commit();
        } catch (PDOException $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function notifcationHit() : SupportCollection {

        // $url = 'https://mis.twse.com.tw/stock/api/getStockInfo.jsp?ex_ch=tse_0050.tw%7Ctse_2356.tw%7Ctse_2330.tw%7Ctse_2317.tw%7Ctse_1216.tw%7Cotc_6547.tw%7Cotc_6180.tw';
        $url = 'https://api.fugle.tw/marketdata/v1.0/stock/intraday/quote/2884';
        $allStock = $this->fetchStockDataFromUrl($url);
        return $allStock;
    }

    /**
     * 推薦購買
     *
     * @return void
     * @author ZhiYong
     */
    public function recommendBuy() : void {
        // 準備call api
        $focus = $this->focusStockRepo->selectAllFocusStockByUserID('testUserID')->pluck('stockCode');
        foreach ($focus as $key => $code) {
            $url = sprintf($this->fugleStockQuote, $code);
            $data = $this->fetchStockDataFromUrl($url);
            $bids = array_column($data['bids'], 'price');
            // 期望值 開盤家 +- 1%
            $wantHightPrice = $data['openPrice'] + ($data['openPrice'] * 0.01);
            $wantLostPrice = $data['openPrice'] - ($data['openPrice'] * 0.01);
            // 以最後一筆交易價格判斷
            if($wantLostPrice <= $data['lastPrice'] && $data['lastPrice'] <= $wantHightPrice){
                $msg[] = "【推薦購買通知】"."\n".
                "日期：" . date('Y-m-d H:i:s') ."\n".
                "個股名稱：". $data['name']. "(" . $data['symbol'] .")"."\n".
                "〖平均價格〗". $data['avgPrice']."\n".
                "〖開盤價格〗". $data['openPrice']."\n".
                "〖最低價格〗". $data['lowPrice']."\n".
                "〖期望最高價格〗". $wantHightPrice."\n".
                "〖期望最低價格〗". $wantLostPrice."\n".
                "〖最佳五檔委買〗". implode(", ", $bids);
            }
        }
        if(!empty($msg)){
            $this->botPush->sendNotification($msg);
        }

    }

    /**
     * 新增關注
     *
     * @param Collection $focus
     * @param Collection $stocks
     * @param array $post
     * @return void
     * @author ZhiYong
     */
    private function createFocus(Collection $focus, Collection $stocks, array $post){
        // 新增關注內容
        $existingStockCodes = $focus->pluck('stockCode')->toArray();
        $newStockCodes = array_diff($post['stockCode'], $existingStockCodes);
        foreach ($newStockCodes as $key => $itme) {
            $info = $stocks->where('stockCode', $itme)->first();
            $create[] = [
                'focusID'       => $this->focusStockRepo->createUniqueID(),
                'userID'        => $post['userID'],
                'stockCode'     => $info->stockCode,
                'name'          => $info->name,
            ];
        }
        if(!empty($create)){
            $this->focusStockRepo->insertData($create);
        }
    }

    /**
     * 刪除關注
     *
     * @param Collection $focus
     * @param array $post
     * @return void
     * @author ZhiYong
     */
    private function deleteFocus(Collection $focus, array $post) : void {
        // 刪除關注內容
        $deleteStockCodes = $focus->pluck('stockCode')->diff($post['stockCode']);
        if(!empty($deleteStockCodes)){
            $this->focusStockRepo->deleteDataByColumn('stockCode', $deleteStockCodes->toArray());
        }
    }
}
