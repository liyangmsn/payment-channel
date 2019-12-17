<?php

namespace PaymentChannel\Official;

use PaymentChannel\Kernel\ServiceContainer;

/**
 * Class Application.
 * @property \PaymentChannel\Official\Wechat\Application            $wechat
 * @property \PaymentChannel\Official\Alipay\Application            $alipay
*/
class Application extends ServiceContainer {
    protected $providers = [
        Wechat\ServiceProvider::class,
        Alipay\ServiceProvider::class,
    ];
}