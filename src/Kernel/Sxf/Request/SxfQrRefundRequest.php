<?php
namespace PaymentChannel\Kernel\Sxf\Request;

class SxfQrRefundRequest {
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
        return "refund";
    }
}
