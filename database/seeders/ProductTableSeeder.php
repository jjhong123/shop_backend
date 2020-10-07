<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'ppid' => "P2020100800281",
            'name' => '衣服一',
            'unit' => '件',
            'count' => '1',
            'content' => '世界最偉大的設asda計師親手設計,xxx.<br>世界最偉大asdasdasd的設計師親手設計,xxx.<br>',
            'price' => '400',
            'origin_price' => '350',
            'status' => '1',
            'category' => '衣服',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/muX46J.jpg'
        ]);

        Product::create([
            'ppid' => "P2020100800282",
            'name' => '衣服二',
            'unit' => '件',
            'count' => '34',
            'content' => '世界最偉大的設asda計師親手設計,xxx.<br>世界最偉大asdasdasd的設計師親手設計,xxx.<br>',
            'price' => '500',
            'origin_price' => '300',
            'status' => '1',
            'category' => '衣服',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/muX46J.jpg'
        ]);

        Product::create([
            'ppid' => "P2020100800283",
            'name' => '衣服三',
            'unit' => '件',
            'count' => '22',
            'content' => '世界最偉大的設asda計師親手設計,xxx.<br>世界最偉大asdasdasd的設計師親手設計,xxx.<br>',
            'price' => '350',
            'origin_price' => '300',
            'status' => '1',
            'category' => '衣服',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/muX46J.jpg'
        ]);

        Product::create([
            'ppid' => "P2020100800284",
            'name' => '衣服四',
            'unit' => '件',
            'count' => '11',
            'content' => '世界最偉大的設asda計師親手設計,xxx.<br>世界最偉大asdasdasd的設計師親手設計,xxx.<br>',
            'price' => '250',
            'origin_price' => '200',
            'status' => '1',
            'category' => '衣服',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/muX46J.jpg'
        ]);

    }
}
