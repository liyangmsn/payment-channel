<?php

namespace PaymentChannel\Kernel;

class Alipay {
    public $aop;

    public function __construct($config) {
        $this->aop = new \AopClient();
        $this->aop->gatewayUrl = $config['gateway_url'];
        $this->aop->appId = $config['app_id'];
        $this->aop->rsaPrivateKey = $config['merchant_private_key'];
        $this->aop->alipayrsaPublicKey = $config['alipay_public_key'];
        $this->aop->format = 'json';
        $this->aop->postCharset = $config['charset'];
        $this->aop->signType = $config['sign_type'];
    }

    public function execute($request, $authToken = null, $appInfoAuthtoken = null, $targetAppId = null) {
        $result = $this->aop->execute($request, $authToken, $appInfoAuthtoken, $targetAppId);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        return $result->$responseNode;
    }
}