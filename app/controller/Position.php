<?php
namespace app\controller;

use app\BaseController;
use app\model\Position as PositionModel;
use app\util\Res;
use think\Request;

class Position extends BaseController{
    private $result;

    function __construct(\think\App $app){
        $this->result = new Res();
    }

}