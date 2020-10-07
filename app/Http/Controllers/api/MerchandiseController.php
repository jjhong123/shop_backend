<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use DB;
class MerchandiseController extends Controller
{
    /**
     * 新增產品
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  name  $name
     * @param  price  $name
     * @param  count  $name
     * @return \Illuminate\Http\Response
     */
    public function createProduct(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'count' => 'required',
            'price' => 'required',
            'unit' => 'required',
            'content' => 'required',
            'origin_price' => 'required',
            'category' => 'required',
            'description' => 'required',
            'pimg' => 'null',
        ]);

        try {
            $results = DB::table('product')->count();
            $product = Product::create([
                'ppid' => 'P' . date("Y") . date("m") . date("d") . date("H") . date("i") . ($results + 1),
                'name' => $request['name'],
                'count' => $request['count'],
                'price' => $request['price'],
                'unit' => $request['unit'],
                'content' => $request['content'],
                'origin_price' => $request['origin_price'],
                'category' => $request['category'],
                'status' => 0,
                'description' => $request['description'],
                'pimg' => null,
            ]);
            return response()->json($product, 200);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json($product, 200);
    }

    /**
     * 取得產品
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProduct(Request $request)
    {
        // 每頁資料量
        $row_per_page = 10;

        // 撈取商品分頁資料
        $ProductPaginate = Product::OrderBy('updated_at', 'desc')
            ->paginate($row_per_page);

        // 設定商品圖片網址
        foreach ($ProductPaginate as &$Product) {
            if (!is_null($Product->photo)) {
                // 設定商品照片網址
                $Product->pimg = url($Product->pimg);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $ProductPaginate,
        ], 200);
    }

    /**
     * 更新產品
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  id  $name
     * @param  name  $name
     * @param  count  $count
     * @return \Illuminate\Http\Response
     */
    public function updateProduct(Request $request)
    {
        $request->validate([
            'ppid' => 'required',
        ]);

        $product_data = Product::where('ppid', $request->ppid)->firstOrFail();

        if ($product_data) {
            $data['product_data'] = $product_data->update($request->all());
            $data['type'] = true;
            // $data['cart'] = $cart;
            return response()->json($data, 200);
        } else {
            throw ValidationException::withMessages([
                'error' => ['找不到此產品'],
            ]);
        }
    }

    /**
     * 刪除產品
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  id  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct(Request $request)
    {
        $request->validate([
            'ppid' => 'required',
        ]);
        $deletedRows = Product::where('id', $request->ppid)->delete();
        return response()->json("已刪除 " . $deletedRows . " 項產品", 200);
    }
}
