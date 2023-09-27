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
use PDO;
use PDOException;

class StockMarketService {

    // private $stockAllDayUrl = 'https://openapi.twse.com.tw/v1/exchangeReport/STOCK_DAY_ALL';
    // private $stockAllDayUrl = 'https://mis.twse.com.tw/stock/api/getStockInfo.jsp?json=1&delay=0&ex_ch=';
    // private $stockAllDayUrl = 'https://api.fugle.tw/marketdata/v1.0/stock/intraday/tickers?type=EQUITY&exchange=TWSE&isNormal=true';
    // private $stockAllDayUrl = 'https://openapi.twse.com.tw/v1/exchangeReport/BWIBBU_ALL';
    // tse_2356.tw

    private $headers = [
        'Content-Type: application/json',
        // 'X-API-KEY: ZWFhNGRjY2YtYzA2ZS00M2VmLTk3N2MtNzE4MmI3YWYyZTJlIDViMWQ5NWE2LTMzN2QtNDk0Yy04YjdmLTkxMWQ4OTgzZTA3Zg=='
    ];

    private $focusCode = ['2356', '2884', '00878'];

    private $stockAllDayUrl;
    private $stockBwibbuUrl;
    public function __construct(
        private LineBotService $botPush,
        private StockRepository $stockRepo,
        private FocusStockRepository $focusStockRepo,
        private UserRepository $userRepo
    ){
        $this->stockAllDayUrl =  config('stockUrl.STOCK_DAY_ALL');
        $this->stockBwibbuUrl =  config('stockUrl.BWIBBU_ALL');
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

    private function fetchStockDataFromUrl(string $url): SupportCollection {
        $result = curl('get', $url, $this->headers);
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
        $data = $this->fetchStockDataFromUrl($this->stockAllDayUrl);
        foreach ($this->focusCode as $key => $value) {
            $notie = $data->where('c', $value)->first();
            $msg[] = "【通知】"."\n".
            "日期：" . date('Y-m-d H:i:s') ."\n".
            "個股名稱：". $notie->n. "(" . $notie->c .")"."\n".
            "開盤價〖高於〗最低價格"."\n".
            "〖開盤價格〗 $notie->o"."\n".
            "〖現價價格〗 $notie->z"."\n".
            "〖最低價格〗 $notie->l";
        }
        return response()->json($msg);
    }

    /**
     * 基礎資料建置
     *
     * @return void
     * @author ZhiYong
     */
    public function createBaseStockInfo() : void {
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
        $this->stockRepo->create($create);
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
