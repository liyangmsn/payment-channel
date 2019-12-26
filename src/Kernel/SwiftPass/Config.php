<?php
namespace PaymentChannel\Kernel\SwiftPass;
class Config{
    private $cfg = array(
        'url'=>'',     /*支付接口请求地址，请联系技术支持确认 */
        'mchId'=>'',   /* 商户号，建议用正式的，于申请成功后的开户邮件中获取，若未开户需用测试的请联系技术支持 */
        'version'=>'2.0',
        'sign_type'=>'RSA_1_256',
        'public_rsa_key'=>'',   /* RSA验签平台公钥，建议用正式的，登录商户后台查看，若未开户需用测试的请联系技术支持 */
        'private_rsa_key'=>''   /* RSA签名私钥，建议用正式的，商户开发人员本地生成，若未开户需用测试的请联系技术支持 */
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}