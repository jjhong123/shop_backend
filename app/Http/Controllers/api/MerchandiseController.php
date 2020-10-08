<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use DB;
use Illuminate\Http\Request;
use Image;

class MerchandiseController extends Controller
{
    // 上傳圖片
    public function uploadImg(Request $request)
    {

        $Product = Product::where('ppid', $request->ppid)->firstOrFail();

        $input = request()->all();

        $rules = [
            // 商品照片
            'photo' => [
                'file', // 必須為檔案
                'image', // 必須為圖片
                'max: 10240', // 10 MB
            ],
        ];

        // 驗證資料
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            // 資料驗證錯誤
            return ['success' => false, 'error_Message' => $validator->errors()->all()];
        }

        if ($request->hasFile('photo')) {
            // $fileName = $file->getClientOriginalName();
            if (isset($input['photo'])) {
                // 有上傳圖片
                $photo = $input['photo'];
                // 檔案副檔名
                $file_extension = $photo->getClientOriginalExtension();
                // 產生自訂隨機檔案名稱
                $file_name = uniqid() . '.' . $file_extension;
                // 檔案相對路徑
                $file_relative_path = 'images/merchandise/' . $file_name;
                // 檔案存放目錄為對外公開 public 目錄下的相對位置
                $file_path = public_path($file_relative_path);
                // 裁切圖片
                // $image = Image::make($photo)->fit(450, 300)->save($file_path);
                $image = Image::make($photo)->save($file_path);
                // 設定圖片檔案相對位置
                $input['photo'] = $file_relative_path;
                // 商品資料更新
                // $Product->update(['pimg' => $input['photo']]);
                $Product->update(['pimg' => url($input['photo'])]);
                // 設定路經
                $Product["pimg"] = url($Product["pimg"]);

                return response()->json([
                    'success' => true,
                    'fileName' => $file_name,
                    'data' => $Product,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'type' => '格式錯誤',
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'type' => '格式錯誤',
        ], 500);
    }

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

    /**
     * 取得購物車
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCart(Request $request)
    {
        $cart_data = DB::select('select * from cart where uuid = ?', [$request->user()->uuid]);
        $data["cart_list"] = $cart_data;
        $data["total_price"] = 0;

        foreach ($cart_data as $value) {
            // 如果 購物車
            $data["total_price"] = $data["total_price"] + ($value->count * $value->price);
        }
        
        return response()->json($data, 200);
    }

    /**
     * 新增購物車
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createCart(Request $request)
    {
        $request->validate([
            'ppid' => 'required',
        ]);

        $product_data = DB::select('SELECT * FROM product WHERE ppid = ?', [$request->ppid]);

        $cart_data = DB::select('SELECT * FROM cart WHERE ppid = ? AND uuid = ?', [$request->ppid, $request->user()->uuid]);

        if (empty($cart_data)) {
            $data["result"] = DB::insert('insert into cart (uuid,ppid,name,category,unit,description,content,pimg,price,count) values (?,?,?,?,?,?,?,?,?,?) ', [
                $request->user()->uuid,
                $product_data[0]->ppid,
                $product_data[0]->name,
                $product_data[0]->category,
                $product_data[0]->unit,
                $product_data[0]->description,
                $product_data[0]->content,
                $product_data[0]->pimg,
                $product_data[0]->price,
                1,
            ]);
            return response()->json($data, 200);

        } else {
            return response()->json(["message" => '已在購物車裡'], 500);
        }

    }

    /**
     * 更新購物車
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  id  $id
     * @param  count  $count
     * @param  type  $type
     * @return \Illuminate\Http\Response
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'ppid' => 'required',
            'count' => 'required',
        ]);

        if (isset($request->count)) {

            $remaining_count = DB::select('select count from product where ppid = ?', [$request->ppid]);

            if ($remaining_count[0]->count >= $request->count) {

                $data["results"] = DB::table('cart')
                    ->where('uuid', $request->user()->uuid)
                    ->where('ppid', $request->ppid)
                    ->update(['count' => $request->count]);

                return response()->json($data, 200);

            } else {
                return response()->json([
                    "message" => '庫存最多' . $remaining_count[0]->count,
                ], 500);
            }

        }

    }

    /**
     * 刪除購物車
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  id  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteCart(Request $request)
    {
        $request->validate([
            'ppid' => 'required',
        ]);

        $remaining_count = DB::select('select * from cart where ppid = ? and uuid = ?', [$request->ppid, $request->user()->uuid]);

        if (!empty($remaining_count)) {

            $data["cart"] = DB::table('cart')
                ->where('uuid', $request->user()->uuid)
                ->where('ppid', $request->ppid)
                ->delete();

            return response()->json($data, 200);
        } else {
            throw ValidationException::withMessages([
                'error' => ['找不到此產品'],
            ]);
        }
    }
}
