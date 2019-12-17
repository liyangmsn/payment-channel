<?php
namespace PaymentChannel\Official\Alipay;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    public function register(Container $app) {
        $app['alipay'] = function ($app) {
            return new Application($app);
        };
    }
}