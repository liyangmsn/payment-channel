<?php


namespace PaymentChannel\Kernel\SwiftPass;

use function EasyWeChat\Kernel\Support\get_client_ip;

class Client {
    private $pay = null;
    private $config = [];

    public function __construct(array $config) {
        $this->pay = new PayHttpClient();
        $this->config = $config;
    }

    public function execute(array $params, string $endpoint) {
        $reqHandler = new RequestHandler();
        $resHandler = new ClientResponseHandler();
        $reqHandler->setGateUrl($this->config['url']);
        $reqHandler->setSignType($this->config['sign_type']);
        $resHandler->setSignType($this->config['sign_type']);
        if ($this->config['sign_type'] == 'MD5') {
            $reqHandler->setKey($this->config['key']);
            $resHandler->setKey($this->config['key']);
        } else if ($this->config['sign_type'] == 'RSA_1_1' || $this->config['sign_type'] == 'RSA_1_256') {
            $reqHandler->setRSAKey("-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($this->config['private_rsa_key'], 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----");
            $resHandler->setRSAKey("-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($this->config['public_rsa_key'], 64, "\n", true) .
                "\n-----END PUBLIC KEY-----");
        }
        $reqHandler->setReqParams($params, [
            'url',
            'private_rsa_key',
            'public_rsa_key'
        ]);
        $reqHandler->setParameter('service', $endpoint);//接口类型：unified.trade.micropay
        $reqHandler->setParameter('mch_id', $this->config['mch_id']);//必填项，商户号，由平台分配
        $reqHandler->setParameter('version', $this->config['version']);
        $reqHandler->setParameter('sign_type', $this->config['sign_type']);
        $reqHandler->setParameter('nonce_str', mt_rand());//随机字符串，
        $reqHandler->setParameter('mch_create_ip', get_client_ip());
        $reqHandler->createSign();//创建签名
        $data = Utils::toXml($reqHandler->getAllParameters());
        $this->pay->setReqContent($reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $resHandler->setContent($this->pay->getResContent());
            $res = $resHandler->getAllParameters();
            return $res;
        }
    }
}