<?php

namespace PaymentChannel\Official\Wechat;

use PaymentChannel\Contracts\PaymentInterface;
use PaymentChannel\Kernel\BaseApplication;
use EasyWeChat\Payment\Application as Payment;

class Application extends BaseApplication implements PaymentInterface {
    private $payment;

    public function __construct(\PaymentChannel\Official\Application $app) {
        parent::__construct($app);
        $config = $this->app->getConfig();
        $this->payment = new Payment($config['wechat']);
    }

    public function pay(array $options): array {
        return $this->payment->pay($options);
    }

    public function unify(array $options): array {
        return $this->payment->order->unify($options);
    }

    public function query(string $out_trade_no): array {
        return $this->payment->order->queryByOutTradeNumber($out_trade_no);
    }

    public function refund(string $out_trade_no, string $refund_no, int $total_fee, int $refund_fee, array $config): array {
        return $this->payment->refund->byOutTradeNumber($out_trade_no, $refund_no, $total_fee, $refund_fee, $config);
    }
}