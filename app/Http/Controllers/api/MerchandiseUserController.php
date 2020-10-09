<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use \ECPay_PaymentMethod as ECPayMethod;

class MerchandiseUserController extends Controller
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
        $ProductPaginate = Product::where('status', 1)->orderBy('updated_at', 'desc')
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
            'product_list' => $ProductPaginate,
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
            $count = DB::select('select count from product where ppid = ?', [$value->ppid]);
            $value->remaining_number = $count[0]->count;
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

        if (empty($cart_data) && !empty($product_data)) {
            $data["result"] = DB::insert('insert into cart (uuid,ppid,name,category,unit,description,content,pimg,price,count,created_at,updated_at) values (?,?,?,?,?,?,?,?,?,?,?,?) ', [
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
                Carbon::now(),
                Carbon::now(),
            ]);
            return response()->json($data, 200);

        } else {
            return response()->json(["message" => '已在購物車裡,或不存在此商品'], 500);
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

    /**
     * 新增訂單
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createOrder(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        $cart_data = DB::select('select * from cart where uuid = ?', [$request->user()->uuid]);

        $data["cart_list"] = $cart_data;
        $data["total_price"] = 0;

        if (!empty($cart_data)) {

            foreach ($cart_data as $value) {
                $data["total_price"] = $data["total_price"] + ($value->count * $value->price);
            }

            $results = DB::table('order')->count();
            $ooid = "O" . date("Y") . date("m") . date("d") . date("H") . date("i") . ($results + 1);
            $uuid = $request->user()->uuid;

            $data["result"] = DB::insert('INSERT INTO `order` (ooid,uuid,total,status,email,name,phone,address,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?) ', [
                $ooid,
                $uuid,
                $data["total_price"],
                0,
                $request->user()->email,
                $request->name,
                $request->phone,
                $request->address,
                Carbon::now(),
                Carbon::now(),
            ]);

            // 綠界
            try {
                $obj = new \ECPay_AllInOne();

                //服務參數
                $obj->ServiceURL = "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5"; //服務位置
                $obj->HashKey = '5294y06JbISpM5x9'; //測試用Hashkey，請自行帶入ECPay提供的HashKey
                $obj->HashIV = 'v77hoKGq4kWxNNIS'; //測試用HashIV，請自行帶入ECPay提供的HashIV
                $obj->MerchantID = '2000132'; //測試用MerchantID，請自行帶入ECPay提供的MerchantID
                $obj->EncryptType = '1'; //CheckMacValue加密類型，請固定填入1，使用SHA256加密
                //基本參數(請依系統規劃自行調整)
                $MerchantTradeNo = Str::random(10);
                $obj->Send['ReturnURL'] = "https://476c9a3056a8.ngrok.io/github/shop_backend/public/api/v1/callback"; //付款完成通知回傳的網址
                $obj->Send['PeriodReturnURL'] = "https://476c9a3056a8.ngrok.io/github/shop_backend/public/api/v1/callback"; //付款完成通知回傳的網址
                $obj->Send['ClientBackURL'] = " https://476c9a3056a8.ngrok.io/github/shop_backend/public/api/v1/success"; //付款完成通知回傳的網址
                $obj->Send['MerchantTradeNo'] = $MerchantTradeNo; //訂單編號
                $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s'); //交易時間
                $obj->Send['TotalAmount'] = $data["total_price"]; //交易金額
                $obj->Send['TradeDesc'] = "good to drink"; //交易描述
                $obj->Send['ChoosePayment'] = ECPayMethod::Credit; //付款方式:Credit
                $obj->Send['IgnorePayment'] = ECPayMethod::GooglePay; //不使用付款方式:GooglePay
                $obj->Send['CustomField1'] = $ooid;
                $obj->Send['CustomField2'] = $uuid;

                foreach ($cart_data as $value) {

                    $cart_data = DB::insert('INSERT INTO order_item (uuid,ooid,ppid,name,price,count,pimg,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)', [
                        $uuid,
                        $ooid,
                        $value->ppid,
                        $value->name,
                        $value->price,
                        $value->count,
                        $value->pimg,
                        Carbon::now(),
                        Carbon::now(),
                    ]);

                    array_push($obj->Send['Items'], array(
                        'Name' => $value->name, 'Price' => $value->price,
                        'Currency' => "元", 'Quantity' => (int) $value->count, 'URL' => "dedwed",
                    ));

                    $data["product"] = DB::table('product')
                        ->where('ppid', $request->ppid)
                        ->update(['count' => $value->count]);
                }
                $data["cart"] = DB::table('cart')
                    ->where('uuid', $request->user()->uuid)
                    ->delete();
                return response()->json($obj->CheckOutString(), 200);
            } catch (Exception $e) {
                echo $e->getMessage();
            }

        } else {
            return response()->json('購物車不得為空', 200);
        }
    }

    /**
     * 取得所有訂單
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getOrder(Request $request)
    {
        // 每頁資料量
        $row_per_page = 10;

        // 撈取商品分頁資料
        $OrderPaginate = DB::table('order')->paginate($row_per_page);

        foreach ($OrderPaginate as $value) {
            $value->cr_at = Carbon::parse($value->created_at)->diffForHumans();
            $value->products = DB::select('select * from order_item where uuid = ? and ooid = ?', [$value->uuid, $value->ooid]);
        }
        return response()->json([
            'success' => true,
            'data' => $OrderPaginate,
        ], 200);
    }

    public function callback(Request $request)
    {

        $data["order"] = DB::table('order')
            ->where('ooid', $request["CustomField1"])
            ->where('uuid', $request["CustomField2"])
            ->update(['status' => 1]);

        $order_item_data = DB::select('select * from order_item where ooid = ? and uuid = ?', [$request["CustomField1"], $request["CustomField2"]]);

        foreach ($order_item_data as $value) {

            $product_data = DB::select('select count from product where ppid = ?', [$value->ppid]);

            $data["product"] = DB::table('product')
                ->where('ppid', $value->ppid)
                ->update(['count' => $product_data[0]->count - $value->count]);
        }

        return response()->json('success', 200);
    }

    public function redirectFromECpay()
    {
        return response()->json('付款完成 Order success!', 200);
    }
}
