<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('ppid')->nullable();// ppid
            $table->string('name');// 名字
            $table->string('category');// 商品敘述
            $table->string('unit');// 單位 ex:個
            $table->string('description');// 產品說明
            $table->text('content');// 產品敘述
            $table->integer('status');// 是否上架
            $table->string('pimg')->nullable();
            $table->integer('price');// 售價
            $table->integer('origin_price');// 原價
            $table->integer('count');// 數量
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
}
