<?php

namespace app\controller;

use app\BaseController;
use think\Request;
use app\util\Res;
use DateTime;
use GuzzleHttp\Client;

class Okx extends BaseController
{
    private $result;

    private $client;

    private $url;

    public function __construct(\think\App $app)
    {
        $this->result = new Res();

        $api_key = "f5890ab2-a9c8-45bf-a91d-6010be64efbe";
        $secret_key = "ADC1875DA3B14F1BF650EF29BF652E43";
        $passphrase = "XQBxqb123@";

        // 设置时区为UTC
        date_default_timezone_set('UTC');

        // 获取当前时间的 DateTime 对象
        $dateTime = new DateTime();

        // 格式化时间戳为指定的格式（ISO 8601）
        $timestamp = $dateTime->format('Y-m-d\TH:i:s.u\Z');

        $url = "https://www.okx.com";

        $body = "";

        $string = $timestamp . "GET" . $url . $body;

        $signature = base64_encode(hash_hmac('sha256', $string, $secret_key, true));

        $headers = [
            "OK-ACCESS-KEY" => $api_key,
            "OK-ACCESS-SIGN" => $signature,
            "OK-ACCESS-TIMESTAMP" => $timestamp,
            "OK-ACCESS-PASSPHRASE" => $passphrase
        ];

        $this->client = new Client([
            'verify' => false,
            'proxy' => 'http://127.0.0.1:23457',
            'headers' => $headers
        ]);
    }

    public function test()
    {
        $res = $this->client->get("https://www.okx.com/api/v5/public/instruments?instType=SPOT")
            ->getBody()
            ->getContents();

        return $this->result->success("获取数据成功", json_decode($res));
    }

    public function getPrice($type)
    {
        $style = strtoupper($type);
        $res = $this->client->get("https://www.okx.com/api/v5/public/mark-price?instType=SWAP&instId={$style}-USDT-SWAP")
            ->getBody()
            ->getContents();

        return $this->result->success("获取数据成功", json_decode($res));
    }
}
