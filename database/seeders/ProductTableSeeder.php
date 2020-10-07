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
            'name' => '衣服一',
            'count' => '1',
            'price' => '400',
            'status' => 'true',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/muX46J.jpg'
        ]);

        Product::create([
            'name' => '衣服一',
            'count' => '3',
            'price' => '200',
            'status' => 'true',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/neXyEo.jpg'
        ]);

        Product::create([
            'name' => '衣服三',
            'count' => '2',
            'price' => '100',
            'status' => 'true',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/kN178P.jpg'
        ]);

        Product::create([
            'name' => '衣服四',
            'count' => '4',
            'price' => '354',
            'status' => 'true',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/muX46J.jpg'
        ]);

        Product::create([
            'name' => '衣服五',
            'count' => '1',
            'price' => '400',
            'status' => 'true',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/muX46J.jpg'
        ]);

        Product::create([
            'name' => '衣服六',
            'count' => '3',
            'price' => '200',
            'status' => 'true',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/neXyEo.jpg'
        ]);

        Product::create([
            'name' => '衣服七',
            'count' => '2',
            'price' => '100',
            'status' => 'true',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/kN178P.jpg'
        ]);

        Product::create([
            'name' => '衣服八',
            'count' => '4',
            'price' => '354',
            'status' => 'true',
            'description' => '世界最偉大的設計師親手設計,xxx.',
            'pimg' => 'https://upload.cc/i1/2020/09/23/muX46J.jpg'
        ]);
    }
}
