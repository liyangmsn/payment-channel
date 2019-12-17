<?php
namespace PaymentChannel\Official\Wechat;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    public function register(Container $app) {
        $app['wechat'] = function ($app) {
            return new Application($app);
        };
    }
}