<?php

namespace PaymentChannel\Contracts;

interface PaymentInterface
{
    public function pay(array $params): array;

    public function unify(array $params, array $options = []): array;

    public function query(string $out_trade_no): array;

    public function refund(string $out_trade_no, string $refund_no, float $total_fee, float $refund_fee, array $config = []): array;
}
