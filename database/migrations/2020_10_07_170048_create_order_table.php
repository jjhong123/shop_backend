<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->string('ooid');// ppid
            $table->string('uuid');// ppid
            $table->integer('status');// 是否上架
            $table->integer('total');// 售價
            $table->string('email');// email
            $table->string('address');// address
            $table->string('name');// name
            $table->string('phone');// phone
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
        Schema::dropIfExists('order');
    }
}
