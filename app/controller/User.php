<?php

namespace app\controller;

use think\Request;
use app\BaseController;
use app\util\Res;
use app\model\User as UserModel;

class User extends BaseController
{
    private $result;

    public function __construct(\think\App $app)
    {
        $this->result = new Res();
    }

    public function add(Request $request)
    {
        $postData = $request->post();

        $u = UserModel::where("username",$postData["username"])->find();

        if($u){
            return $this->result->error("用户已存在");
        }


        $user = new UserModel([
            "username" => $postData["username"],
            "password" => password_hash($postData["password"], PASSWORD_DEFAULT),
            "add_time" => date("Y-m-d H:i:s")
        ]);

        $res = $user->save();
        if ($res) {
            return $this->result->success("添加用户成功", $user);
        }
        return $this->result->error("添加用户数据失败");
    }

    function setBalance(Request $request)
    {
        $id = $request->post("id");
        $balance = $request->post("balance");

        $user = UserModel::where("id", $id)->find();

        $res = $user->save(["balance" => $balance]);
        if ($res) {
            return $this->result->success("更新余额成功", $user);
        }
        return $this->result->error("更新余额失败");
    }
}
