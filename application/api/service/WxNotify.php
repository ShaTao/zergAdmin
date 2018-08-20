<?php
namespace app\api\service;

use think\Exception;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\api\model\Product;
use think\Db;


require_once "../extend/WxPay/WxPay.Api.php";

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($objData, $config, $msg)
    {
        if ($objData["result_code"] == "SUCCESS") {
            $orderNo = $objData["out_trade_no"];
            Db::startTrans();
            try {
                $order = Order::where("order_no", "=", $order)->find();
                if ($order->status == 1) {
                    $orderService = new OrderService();
                    $stockStatus = $orderService->checkOrderStock($order->id);
                    if ($stockStatus["pass"]) {
                        $this->updateOrderStatus($order->id, true);
                        $this->reduceStock($stockStatus);
                    } else {
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit();
                return true;
            } catch (Exception $ex) {
                Db::rollback();
                return false;
            }
        }else {
            return true;
        }
    }

    private function reduceStock($stockStatus)
    {
        foreach ($stockStatus["pStatusArray"] as $pStatus) {
            Product::where("id", "=", $pStatus["id"])->setDec("stock", $pStatus["count"]);
        }
    }

    private function updateOrderStatus($orderId, $success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_STOCKOUT;
        OrderModel::where("id", "=", $orderId)->update(["status" => $status]);
    }
}