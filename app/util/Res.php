<?php
namespace app\util;

class Res{
    function success($msg,$data){
        return json([
            "code"=>200,
            "msg"=>$msg,
            "data"=>$data
        ]);
    }

    function error($msg){
        return json([
            "code"=>400,
            "msg"=>$msg,
            "data"=>null
        ]);
    }
}