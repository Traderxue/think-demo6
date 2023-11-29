<?php

namespace app\controller;

use app\BaseController;
use app\model\Position as PositionModel;
use app\util\Res;
use think\Request;

class Position extends BaseController
{
    private $result;

    function __construct(\think\App $app)
    {
        $this->result = new Res();
    }

    function open(Request $request)
    {
        $postData = $request->post();

        $position = new PositionModel([
            "open_time" => date("Y-m-d H:i:s"),
            "close_time" => date("Y-m-d H:i:s"),
            "type" => $postData["type"],
            "open_price" => $postData["open_price"],
            "direction" => $postData["direction"],
            "u_id" => $postData["u_id"]
        ]);

        $res = $position->save();

        if ($res) {
            return $this->result->success("开仓成功", $position);
        }
        return $this->result->error("开仓失败");
    }

    function close(Request $request)
    {
        $postData = $request->post();
        $position = PositionModel::where("id",$postData["id"])->find();

        if($position->direction=="多"){
            $profit =(float) $postData["close_price"] - (float) $position->open_price;
        }else{
            $profit = (float) $position->open_price - (float) $postData["close_price"];
        }

        if($profit>0){
            $result = "止盈";
        }else{
            $result = "止损";
        }

        $res = $position->save([
            "close_time"=>date("Y-m-d H:i:s"),
            "close_price"=>$postData["close_price"],
            "profit"=>$profit,
            "result"=>$result
        ]);

        if($res){
            return $this->result->success("平仓成功",$position);
        }
        return $this->result->error("平仓失败");
    }

    function page(Request $request){
        $page = $request->param("page",1);
        $pageSize = $request->param("pageSize",10);
        $result = $request->param("result");

        $list= PositionModel::where("result","like","%{$result}%")->paginate([
            "page"=>$page,
            "list_rows"=>$pageSize
        ]);

        return $this->result->success("获取数据成功",$list);
    }

    function getByUid($u_id){
        $list = PositionModel::where("u_id",$u_id)->select();
        return $this->result->success("获取数据成功",$list);
    }

    function deleteById($id){
        $res = PositionModel::where("id",$id)->delete();
        if($res){
            return $this->result->success("删除数据成功",$res);
        }
        return $this->result->error("删除数据失败");
    }

}
