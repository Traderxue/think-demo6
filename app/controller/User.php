<?php

namespace app\controller;

use think\Request;
use app\BaseController;
use app\util\Res;
use app\model\User as UserModel;
use think\facade\Db;

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

        $u = UserModel::where("username", $postData["username"])->find();

        if ($u) {
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

    function disable($id)
    {
        $user = UserModel::where("id", $id)->find();
        $res = $user->save(["state" => 1]);
        if ($res) {
            return $this->result->success("禁用用户成功", $user);
        }
        return $this->result->error("禁用失败");
    }

    function login(Request $request)
    {
        $username = $request->post("username");
        $password = $request->post("password");
        $user = UserModel::where("username", $username)->find();

        if ($user == null) {
            return $this->result->error("用户不存在");
        }

        if (password_verify($password, $user->password)) {
            return $this->result->success("登录成功", $user);
        } else {
            return $this->result->error("登录失败");
        }
    }

    function transfer(Request $request)
    {
        $from_id = $request->post("from_id");
        $to_username = $request->post("to_username");
        $amount = $request->post("amount");

        Db::startTrans();
        try {
            $from_user = UserModel::where("id", $from_id)->find();
            $to_user = UserModel::where("username", $to_username)->find();

            if ((float) $from_user->balance < (float) $amount) {
                return $this->result->error("用户余额不足");
            }
            $from_user->save(["balance" => (float)$from_user->balance - (float)$amount]);
            $to_user->save(["balance" => (float)$to_user->balance + (float)$amount]);
            Db::commit();
        } catch (\Throwable $th) {
            Db::rollback();
            return $this->result->error("转账失败"+$th);
        }
        return $this->result->success("转账成功",null);
    }

    function page(Request $request){
        $page = $request->param("page",1);
        $pageSize = $request->param("pageSize",10);
        $username = $request->param("username");

        $list = UserModel::where("username","like","%{$username}%")->paginate([
            "page"=>$page,
            "list_rows"=>$pageSize
        ]);

        return $this->result->success("获取数据成功",$list);
    }

    function getByUid($id){
        $user = UserModel::where("id",$id)->find();
        if($user==null){
            return $this->result->error("用户不存在");
        }
        return $this->result->success("获取数据成功",$user);
    }
}
