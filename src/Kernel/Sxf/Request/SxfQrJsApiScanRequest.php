<?php
namespace PaymentChannel\Kernel\Sxf\Request;

class SxfQrJsApiScanRequest {
    private $bizContent;

    private $apiParas = array();


    public function setBizContent($bizContent) {
        $this->bizContent = $bizContent;
        $this->apiParas["biz_content"] = $bizContent;
    }

    public function getBizContent() {
        return $this->bizContent;
    }

    public function getApiMethodName() {
        return "jsapiScan";
    }
}
