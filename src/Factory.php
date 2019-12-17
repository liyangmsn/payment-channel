<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PaymentChannel;

/**
 * Class Factory.
 *
 * @method static \PaymentChannel\Official\Application            official(array $config)
 * @method static \PaymentChannel\Sxf\Application                 sxf(array $config)
 */
class Factory {
    /**
     * @param string $name
     * @param array $config
     *
     * @return \PaymentChannel\Kernel\ServiceContainer
     */
    public static function make($name, array $config) {
        $namespace = Kernel\Support\Str::studly($name);
        $application = "\\PaymentChannel\\{$namespace}\\Application";

        return new $application($config);
    }

    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments) {
        return self::make($name, ...$arguments);
    }
}
