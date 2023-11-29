<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;


Route::group("/user",function(){

    Route::post("/add","user/add");

    Route::post("/balance","user/setBalance");

    Route::get("/disable/:id","user/disable");

    Route::post("/login","user/login");

    Route::post("/transfer","user/transfer");

    Route::post("/page","user/page");

    Route::post("/get/:id","user/getByUid");
});

Route::group("/position",function(){

    Route::post("/open","position/open");

    Route::post("/close","position/close");

    Route::get("/page","position/page");

    Route::delete("/delete/:id","position/deleteById");

    Route::get("/get/:u_id","position/getByUid");
});