<?php


namespace PaymentChannel\Kernel\SwiftPass;


use function PaymentChannel\Kernel\Support\get_client_ip;

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
        if ($this->config['sign_type'] == 'MD5') {
            $reqHandler->setKey($this->config['key']);
            $resHandler->setKey($this->config['key']);
        } else if ($this->config['sign_type'] == 'RSA_1_1' || $this->config['sign_type'] == 'RSA_1_256') {
            $reqHandler->setRSAKey($this->config['private_rsa_key']);
            $resHandler->setRSAKey($this->config['public_rsa_key']);
        }
        $reqHandler->setParameter('service', $endpoint);//接口类型：unified.trade.micropay
        $reqHandler->setParameter('mch_id', $this->config['mchId']);//必填项，商户号，由平台分配
        $reqHandler->setParameter('version', $this->config['version']);
        $reqHandler->setParameter('sign_type', $this->config['sign_type']);
        $reqHandler->setParameter('nonce_str', mt_rand());//随机字符串，
        $reqHandler->setParameter('mch_create_ip', get_client_ip());
        $reqHandler->setReqParams($params);
        $reqHandler->createSign();//创建签名

        $data = Utils::toXml($reqHandler->getAllParameters());
        Utils::dataRecodes(date("Y-m-d H:i:s", time()) . '支付请求XML', $data);//请求xml记录到result.txt
        $this->pay->setReqContent($reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $resHandler->setContent($this->pay->getResContent());
            $resHandler->setKey($reqHandler->getKey());
            $res = $resHandler->getAllParameters();
            return $res;
        }
    }
}