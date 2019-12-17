<?php

namespace PaymentChannel\Kernel;

class BaseApplication {
    protected $app;
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }
}