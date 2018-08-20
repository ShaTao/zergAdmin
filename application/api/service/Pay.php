<?php
namespace app\api\service;

require_once "../extend/WxPay/WxPay.Api.php";
require_once "../extend/WxPay/WxPay.Config.php";
use think\Exception;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\exception\OrderException;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WxChatException;

class Pay
{
    private $orderId;
    private $orderNo;

    function __construct($orderId)
    {
        if (!$orderId) {
            throw new Exception("订单号不能为空");
        }
        $this->orderId = $orderId;
    }

    public function pay()
    {
        $this->checkOrderValid();
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderId);
        if (!$status["pass"]) {
            return $status;
        }
        return $this->makeWxAdvancePay($status["orderPrice"]);
    }

    private function makeWxAdvancePay($totalPrice)
    {
        $openId = Token::getCurrentTokenVar("openid");
        if (!$openId) {
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type("JSAPI");
        $wxOrderData->SetTotal_fee($totalPrice * 100);
        $wxOrderData->SetBody("微信支付测试");
        $wxOrderData->SetOpenid($openId);
        $wxOrderData->SetNotify_url("config(self.pay_back_url)");
        return $this->getPaySignature($wxOrderData);
    }

    private function getPaySignature($wxOrderData)
    {
        $config = new \WxPayConfig();
        $wxOrder = \WxPayApi::unifiedOrder($config, $wxOrderData);
        if ($wxOrder["return_code"] != "SUCCESS" || $wxOrder["result_code"] != "SUCCESS") {
            // throw new Exception("统一下单失败");
            throw new WxChatException([
                "msg" => "微信错误:" . $wxOrder["return_msg"]
            ]);
        }
        $this->recordAdvancePay($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config("self.app_id"));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage("prepay_id=" . $wxOrder["prepay_id"]);
        $jsApiPayData->SetSign("md5");
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues["paySign"] = $sign;
        unset($rawValues["appId"]);
        return $rawValues;
    }

    // private function 

    private function recordAdvancePay($wxOrder)
    {
        OrderModel::where("id", "=", $this->orderId)->update(["prepay_id" => $wxOrder["prepay_id"]]);
    }

    private function checkOrderValid()
    {
        $order = OrderModel::where("id", "=", $this->orderId)->find();
        if (!$order) {
            throw new OrderException();
        }
        if (!Token::isValidOperate($order->user_id)) {
            throw new OrderException([
                "msg" => "订单与用户不匹配",
                "errorCode" => 10003
            ]);
        }
        if ($order->status != OrderStatusEnum::UPAID) {
            throw new OrderException([
                "code" => 400,
                "msg" => "订单已经支付过了，请不要重复支付",
                "errorCode" => 80003
            ]);
        }
        $this->orderNo = $order->order_no;
        return true;
    }
}