<?php

namespace PaymentChannel\Sxf;

use PaymentChannel\Contracts\PaymentInterface;
use PaymentChannel\Kernel\BaseApplication;
use PaymentChannel\Kernel\Sxf\Request\SxfQrJsApiScanRequest;
use PaymentChannel\Kernel\Sxf\Request\SxfQrQueryRequest;
use PaymentChannel\Kernel\Sxf\Request\SxfQrRefundRequest;
use PaymentChannel\Kernel\Sxf\Request\SxfQrReverseScanRequest;
use PaymentChannel\Kernel\Sxf\SxfClient;

class Application extends BaseApplication implements PaymentInterface
{
    private $payment;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->payment = new SxfClient($this->config);
    }

    public function pay(array $params): array
    {
        $request = new SxfQrReverseScanRequest();
        $request->setBizContent(array_merge([
            'mno' => $this->config['mno'],
        ], $params));
        return $this->payment->execute($request);
    }

    public function unify(array $params, array $options = []): array
    {
        $request = new SxfQrJsApiScanRequest();
        $request->setBizContent(array_merge([
            'mno' => $this->config['mno'],
        ], $params));
        return $this->payment->execute($request);
    }

    public function query(string $out_trade_no): array
    {
        $request = new SxfQrQueryRequest();
        $request->setBizContent([
            'mno' => $this->config['mno'],
            'ordNo' => $out_trade_no,
        ]);
        return $this->payment->execute($request);
    }

    public function refund(string $out_trade_no, string $refund_no, float $total_fee, float $refund_fee, array $config = []): array
    {
        $request = new SxfQrRefundRequest();
        $request->setBizContent([
            'mno' => $this->config['mno'],
            'ordNo' => $out_trade_no,
            'origOrderNo' => $out_trade_no,
            'amt' => $refund_fee,
            'notifyUrl' => $this->config['notifyUrl']
        ]);
        return $this->payment->execute($request);
    }
}
