<?php

namespace app\controller;

use app\BaseController;
use think\Request;
use app\model\Coin as CoinModel;
use app\util\Res;
use GuzzleHttp\Client;

class Coin extends BaseController
{
    private $result;
    private $client;

    public function __construct(\think\App $app)
    {
        $this->result = new Res();

        $proxyConfig = [
            'http'  => [
                'port' => '23457',
                'host' => '127.0.0.1',
            ],
            'https' => [
                'port' => '23457',
                'host' => '127.0.0.1',
            ],
        ];

        $this->client = new Client([
            'proxy' => $proxyConfig,
        ]);
    }

    function add(Request $request)
    {
        $coin = new CoinModel([
            "type" => $request->post("type"),
            "add_time" => date("Y-m-d H:i:s")
        ]);
        $res = $coin->save();
        if ($res) {
            return $this->result->success("添加数据成功", $coin);
        }
        return $this->result->error("添加数据失败");
    }

    function edit(Request $request)
    {
        $coin = CoinModel::where("type", $request->post("type"))->find();

        $res = $coin->save([
            "price" => $request->post("price"),
            "parcent" => $request->post("parcent")
        ]);

        if ($res) {
            return $this->result->success("数据编辑成功", $coin);
        }
        return $this->result->error("数据编辑失败");
    }

    function getDetail($type)
    {
        $res = $this->client->get("https://api.huobi.pro/market/detail?symbol={$type}usdt")->getBody()->getContents();
        return $this->result->success("获取币种信息成功", $res);
    }
}
