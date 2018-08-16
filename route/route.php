<?php

// use think\Route;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// Route::rule("路由表达式", "路由地址", "请求类型", "路由参数（数组）", "变量规则（数组）");
// Route::rule("index", "index/Index/index", "GET", ["https"=>false]);
// GET, POST, PUT, DELETE, *

Route::get("api/:version/banner/:id", "api/:version.Banner/getBanner");

Route::get("api/:version/theme", "api/:version.Theme/getSimpleList");
Route::get("api/:version/theme/:id", "api/:version.Theme/getComplexOne");

Route::group("api/:version/product/", function () {
    Route::get("recent", "api/:version.Product/getRecentProduct");
    Route::get(":id", "api/:version.Product/getOne", [], ["id" => "\d+"]);
    Route::get("cate", "api/:version.Product/getProductByCategory");
});

Route::get("api/:version/category/all", "api/:version.Category/getAllCategory");

Route::post("api/:version/token/user", "api/:version.Token/getToken");

Route::post("api/:version/address", "api/:version.Address/createOrUpdateAddress");

Route::post("api/:version/order", "api/:version.Order/placeOrder");

