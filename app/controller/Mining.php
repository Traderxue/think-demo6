<?php

namespace app\controller;

use app\BaseController;
use think\Request;
use app\model\Mining as MiningModel;
use app\util\Res;

class Mining extends BaseController
{
    protected $result;

    public function __construct(\think\App $app)
    {
        $this->result = new Res();
    }

    public function add(Request $request)
    {
        $postData = $request->post();

        $mining = new MiningModel([
            "type" => $postData["type"],
            "income" => $postData["income"],
            "rate" => $postData["rate"],
            "cycle" => $postData["cycle"],
            "mininum" => $postData["mininum"],
            "add_time" => date("Y-m-d H:i:s")
        ]);

        $res = $mining->save();
        if ($res) {
            return $this->result->success("数据添加成功", $mining);
        }
        return $this->result->error("数据添加失败");
    }

    public function edit(Request $request)
    {
        $mining = MiningModel::where("id", $request->post("id"))->find();

        $mining->save([
            "rate" => $request->post("rate"),
            "income" => $request->post("income"),
            "mininum" => $request->post("mininum"),
            "cycle" => $request->post("cycle"),
        ]);

        $res = $mining->save();
        if ($res) {
            return $this->result->success("编辑数成功", $mining);
        }
        return $this->result->error("编辑数据失败");
    }

    public function getAll()
    {
        $list = MiningModel::select();
        return $this->result->success("获取数据成功", $list);
    }

    public function page(Request $request)
    {
        $page = $request->param("page", 1);
        $pageSize = $request->param("pageSize", 10);
        $type = $request->param("type");

        $list = MiningModel::where("type", "like", "%{$type}%")->paginate([
            "page" => $page,
            "list_rows" => $pageSize
        ]);

        return $this->result->success("获取数据成功", $list);
    }

    public function getById($id)
    {
        $mining = MiningModel::where("id", $id)->find();
        return $this->result->success("获取数据成功", $mining);
    }

    public function deleteById($id)
    {
        $res = MiningModel::destroy($id);
        if ($res) {
            return $this->result->success("获取数据成功", $res);
        }
        return $this->result->error("获取数据失败");
    }

}
