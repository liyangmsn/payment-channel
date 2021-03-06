<?php

namespace PaymentChannel\Official\Alipay;

use PaymentChannel\Contracts\PaymentInterface;
use PaymentChannel\Kernel\BaseApplication;
use PaymentChannel\Kernel\Alipay;

class Application extends BaseApplication implements PaymentInterface
{
    private $payment;
    private $auth_token;
    private $app_auth_token;
    private $target_app_id;

    public function __construct(\PaymentChannel\Official\Application $app)
    {
        parent::__construct($app);
        $config = $this->app->getConfig();
        $this->payment = new Alipay($config['alipay']);
        $this->auth_token = $config['alipay']['auth_token'];
        if (array_key_exists('app_auth_token', $config['alipay'])) {
            $this->app_auth_token = $config['alipay']['app_auth_token'];
        }
        if (array_key_exists('target_app_id', $config['alipay'])) {
            $this->target_app_id = $config['alipay']['target_app_id'];
        }
    }

    public function pay(array $params): array
    {
        $request = new \AlipayTradePayRequest();
        $request->setBizContent(json_encode($params));
        $result = $this->payment->execute($request, $this->auth_token, $this->app_auth_token, $this->target_app_id);
        return $this->_object_to_array($result);
    }

    public function unify(array $params, array $options = []): array
    {
        $request = new \AlipayTradeCreateRequest();
        $request->setBizContent(json_encode($params));
        if (array_key_exists('notify_url', $options)) {
            $request->setNotifyUrl($options['notify_url']);
        }
        $result = $this->payment->execute($request, $this->auth_token, $this->app_auth_token, $this->target_app_id);
        return $this->_object_to_array($result);
    }

    public function query(string $out_trade_no): array
    {
        $request = new \AlipayTradeQueryRequest();
        $request->setBizContent(json_encode([
            'out_trade_no' => $out_trade_no
        ]));
        $result = $this->payment->execute($request, $this->auth_token, $this->app_auth_token, $this->target_app_id);
        return $this->_object_to_array($result);
    }

    public function refund(string $out_trade_no, string $refund_no, float $total_fee, float $refund_fee, array $config = []): array
    {
        $request = new \AlipayTradeRefundRequest();
        $request->setBizContent(json_encode(array_merge([
            'out_trade_no' => $out_trade_no,
            '$out_trade_no' => $refund_fee
        ], $config)));
        $result = $this->payment->execute($request, $this->auth_token, $this->app_auth_token, $this->target_app_id);
        return $this->_object_to_array($result);
    }

    private function _object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return null;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)$this->_object_to_array($v);
            }
        }

        return $obj;
    }
}
