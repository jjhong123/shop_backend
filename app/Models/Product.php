<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'product';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ppid',
        'name', // 名字
        'unit', // 單位
        'origin_price', //原價
        'category', // 售價
        'content', // 產品說明
        'price', // 價格
        'count', // 剩餘數量
        'pimg', // 圖片
        'description', // 產品
        'status' // 產品是否上架
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];
}
