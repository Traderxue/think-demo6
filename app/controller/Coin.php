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
        $tunnelProxy = 'http://127.0.0.1:23457'; // 替换为你的隧道代理 URL
        $this->result = new Res();
        $this->client = new Client([
            'verify' => false,
            'proxy' => $tunnelProxy,
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
        $url = "https://api.huobi.pro/market/detail?symbol={$type}usdt";
        $res = $this->client
            ->get($url)
            ->getBody()
            ->getContents();

        $data = json_decode($res);

        $price = $data->tick->close;

        $parcent = number_format(((float) $data->tick->close - (float) $data->tick->open) / (float) $data->tick->open * 100, 2);

        return $this->result->success("获取币种信息成功", $parcent);
    }

    function getKline(Request $request)
    {
        $type = $request->param("type");
        $time = $request->param("time");

        $url = "https://api.huobi.pro/market/history/kline?period={$time}&size=200&symbol={$type}usdt";

        $res = $this->client
            ->get($url)
            ->getBody()
            ->getContents();
        return $this->result->success("获取数据成功", json_decode($res));
    }

    function getDepth($type)
    {
        $url = "https://api.huobi.pro/market/depth?symbol={$type}usdt&depth=5&type=step0";

        $res = $this->client
            ->get($url)
            ->getBody()
            ->getContents();

        return $this->result->success("获取数据成功", json_decode($res));
    }
}
