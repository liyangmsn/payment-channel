<?php

namespace PaymentChannel\SwiftPass;

use PaymentChannel\Contracts\PaymentInterface;
use PaymentChannel\Kernel\BaseApplication;
use PaymentChannel\Kernel\SwiftPass\Client;

class Application extends BaseApplication implements PaymentInterface
{
    private $payment;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->payment = new Client($config);
    }

    public function pay(array $params): array
    {
        return $this->payment->execute($params, 'unified.trade.micropay');
    }

    public function unify(array $params, array $options = []): array
    {
        return $this->payment->execute($params, $options['service']);
    }

    public function query(string $out_trade_no): array
    {
        return $this->payment->execute([
            'out_trade_no' => $out_trade_no
        ], 'unified.trade.query');
    }

    public function refund(string $out_trade_no, string $refund_no, float $total_fee, float $refund_fee, array $config = []): array
    {
        return $this->payment->execute(array_merge([
            'out_trade_no' => $out_trade_no,
            'out_refund_no' => $refund_no,
            'total_fee' => $total_fee,
            'refund_fee' => $refund_fee
        ], $config), 'unified.trade.refund');
    }
}
