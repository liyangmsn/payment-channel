<?php

namespace PaymentChannel\Kernel;

use Pimple\Container;

class ServiceContainer extends Container {
    protected $defaultConfig = [];
    protected $userConfig = [];
    protected $providers = [];

    public function __construct(array $config = [], array $prepends = []) {
        $this->registerProviders($this->getProviders());
        parent::__construct($prepends);
        $this->userConfig = $config;
    }

    public function getConfig() {
        return array_replace_recursive($this->defaultConfig, $this->userConfig);
    }

    public function getProviders() {
        return array_merge([
        ], $this->providers);
    }

    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

    private function registerProviders(array $providers) {
        foreach ($providers as $provider) {
            $this->register(new $provider());
        }
    }
}