<?php

namespace app\model;

use think\Request;
use app\BaseController;
use app\util\Res;
use app\model\User as UserModel;

class User extends BaseController
{
    private $result;

    public function __construct()
    {
        $this->result = new Res();
    }
}
