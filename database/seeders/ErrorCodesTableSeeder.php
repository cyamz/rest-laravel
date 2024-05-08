<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ErrorCodesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('error_codes')->delete();
        
        \DB::table('error_codes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 400,
                'desc' => '请求语法错误',
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 401,
                'desc' => '权限错误',
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 403,
                'desc' => '拒绝访问',
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 405,
                'desc' => '不支持的HTTP方法',
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 409,
                'desc' => '资源正在被使用',
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 410,
                'desc' => '资源已不存在',
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 412,
                'desc' => '提供的数据结构错误',
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 500,
                'desc' => '服务器错误',
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 501,
                'desc' => '请求方法未实现',
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 503,
                'desc' => '服务目前不可用',
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 422,
                'desc' => '语义错误，无法响应',
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 404,
                'desc' => '资源未找到',
            ),
        ));
        
        
    }
}