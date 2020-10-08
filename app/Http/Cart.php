<?php

namespace App\Http;

class Cart
{
    public $cart;
    public $totalCount; //商品總數量
    public $totalPrices; //商品總金額

    public function __construct($cart)
    {

        $this->totalCount = 0;
        $this->totalPrice = 0;
        $this->cart = $cart;

    }

    /**
     * 增加商品到當前購物車
     *
     * @access public
     * @param array $item 商品資訊（一維陣列：array(商品ID,商品名稱,商品單價,商品數量)）
     * @return array 返回當前購物車內商品的陣列
     */
    public function add($item)
    {
        if (!is_array($item) || is_null($item)) {
            return $this->cart;
        }

        if (!is_numeric(end($item)) || (!is_numeric(prev($item)))) {
            echo "價格和數量必須是數字";
            return $this->cart;
        }
        reset($item); //這一句是必須的，因為上面的判斷已經移動了陣列的指標
        $key = current($item);
        if ($key == "") {
            return $this->cart;
        }

        if ($this->_isExists($key)) { //商品是否已經存在？
            $this->cart[$key]['count'] = end($item);
            return $this->cart;
        }
        $this->cart[$key]['ID'] = $key;
        $this->cart[$key]['name'] = next($item);
        $this->cart[$key]['price'] = next($item);
        $this->cart[$key]['count'] = next($item);
        return $this->cart;
    }

    /**
     * 從當前購物車中取出部分或全部商品
     * 當 $key=="" 的時候，清空當前購物車
     * 當 $key!=""&&$count=="" 的時候，從當前購物車中揀出商品ID號為 $key 的全部商品
     * 當 $key!=""&&$count!="" 的時候，從當前購物車中揀出 $count個 商品ID號為 $key 的商品
     *
     * @access public
     * @param string $key 商品ID
     * @return mixed 返回真假或當前購物車內商品的陣列
     */
    public function remove($key = "", $count = "")
    {
        if ($key == "") {
            $this->cart = array();
            return true;
        }
        if (!array_key_exists($key, $this->cart)) {
            return false;
        }

        if ($count == "") { //移去這一類商品
            unset($this->cart[$key]);
        } else { //移去$count個商品
            $this->cart[$key]['count'] -= $count;
            if ($this->cart[$key]['count'] <= 0) {
                unset($this->cart[$key]);
            }

        }
        return $this->cart;
    }

    /**
     * 從當前購物車中取出全部商品
     *
     * @access public
     * @return mixed 返回真假或當前購物車內商品的陣列
     */
    public function getAllcart()
    {
        if ($key == "") {
            $this->cart = array();
            return true;
        }
        if (!array_key_exists($key, $this->cart)) {
            return false;
        }

        if ($count == "") { //移去這一類商品
            unset($this->cart[$key]);
        } else { //移去$count個商品
            $this->cart[$key]['count'] -= $count;
            if ($this->cart[$key]['count'] <= 0) {
                unset($this->cart[$key]);
            }

        }
        return $this->cart;
    }

    /**
     * 判斷當前購物車是否為空，即沒有任何商品
     *
     * @access public
     * @return bool true or false;
     */
    public function isEmpty()
    {
        return !count($this->cart);
    }

    /**
     * 取得當前購物車所有商品的總金額
     *
     * @access public
     * @return float 返回金額;
     */
    public function totalPrices()
    {
        if ($this->_stat()) {
            return $this->totalPrices;
        }
        return 0;
    }
}
