<?php
namespace Modules\Stock\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\stock\Repositories\FocusStockRepository;
use Modules\stock\Repositories\StockRepository;
use PDOException;

class FocusStockService{

    public function __construct(
        private StockRepository $stockRepo,
        private FocusStockRepository $focusStockRepo

    ){}

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
            $focus = $this->focusStockRepo->selectAllData([['method', $post['method']]]);
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
                'method'        => $post['method'],
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
        $delete = $focus->filter(function ($item) use ($post) {
            return !in_array($item['stockCode'], $post['stockCode']);
        });
        $delete = $delete->pluck('focusID')->toArray();
        if(!empty($delete)){
            $this->focusStockRepo->deleteDataByColumn('focusID', $delete);
        }
    }
}