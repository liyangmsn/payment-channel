<?php

namespace PaymentChannel\Contracts;

interface PaymentInterface {
    public function pay(array $options): array;

    public function unify(array $options): array;

    public function query(string $out_trade_no): array;

    public function refund(string $out_trade_no, string $refund_no, int $total_fee, int $refund_fee, array $config): array;
}