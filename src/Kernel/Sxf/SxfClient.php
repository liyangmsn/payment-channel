<?php

namespace PaymentChannel\Kernel\Sxf;

use PaymentChannel\Kernel\Support\Str;

class SxfClient {
    private $orgId;
    private $privateKey;
    private $sxfPublicKey;
    private $signType = 'RSA';
    private $version = '1.0';
    private $apiUrl = 'https://icm-management.suixingpay.com/management/qr/';

    public function __construct($config) {
        $this->orgId = $config['orgId'];
        $this->privateKey = $config['private_key'];
        $this->sxfPublicKey = $config['sxf_public_key'];
        $this->signType = $config['sign_type'];
        if ($config['debug']) {
            $this->apiUrl = 'https://icm-test.suixingpay.com/management/qr/';
        }
    }

    public function execute($request) {
        $body = [
            'orgId' => $this->orgId,
            'reqId' => Str::random(),
            'signType' => $this->signType,
            'timestamp' => date('YmdHis'),
            'version' => $this->version,
            'reqData' => $request->getBizContent()
        ];
        $body['sign'] = $this->rsaSign($this->getSignContent($body), $this->privateKey);
        $result = $this->curl($this->apiUrl . $request->getApiMethodName(), $body);
        $result = json_decode($result, JSON_UNESCAPED_UNICODE);
        if ($result['code'] != 'SXF0000') {
            throw new \Exception($result['msg']);
        }
        $newSign = $result['sign'];
        unset($result['sign']);
        ksort($result);
        $mystrs2 = $this->createLinkString($result);
        if ($this->verify($mystrs2, $newSign, $this->sxfPublicKey)) {
            return $result['respData'];
        } else {
            throw new \Exception('验签失败');
        }
    }

    protected function curl($url, $postFields = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $postBodyString = json_encode($postFields, JSON_UNESCAPED_SLASHES);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postBodyString);

        $headers = [
            'Content-Type: application/json;charset=utf-8',
            'Content-Length: ' . strlen($postBodyString)
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $reponse = curl_exec($ch);

        if (curl_errno($ch)) {

            throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($reponse, $httpStatusCode);
            }
        }

        curl_close($ch);
        return $reponse;
    }

    public function getSignContent($params) {
        //排序
        ksort($params);
        //拼接
        return $this->createLinkString($params);
    }

    function createLinkString($para) {
        $params = array();
        foreach ($para as $key => $value) {
            if (is_array($value)) {
                $value = stripslashes(json_encode($value, JSON_UNESCAPED_UNICODE));
            }
            $params[] = $key . '=' . $value;
        }
        $data = implode("&", $params);

        return $data;
    }

    protected function rsaSign($data, $private_key) {
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($private_key, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        openssl_sign($data, $sign, $res);

        $sign = base64_encode($sign);
        return $sign;
    }

    function verify($data, $sign, $pubKey) {
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        ($res) or die('RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值

        $result = FALSE;
        $result = (openssl_verify($data, base64_decode($sign), $res) === 1);

        return $result;
    }
}