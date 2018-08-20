<?php
namespace app\api\service;

use think\Exception;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct;
use app\lib\exception\UserException;
use app\lib\exception\OrderException;
use think\Db;


class Order
{
    protected $oProducts;
    protected $products;
    protected $uid;

    public function place($uid, $oProducts)
    {
        $this->oProducts = $oProducts;
        $this->products = $this->getProductByOrder($oProducts);
        $this->uid = $uid;
        $status = $this->getOrderStatus();
        if (!$status["pass"]) {
            $status["order_id"] = -1;
            return $status;
        }
        //创建订单
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order["pass"] = true;
        return $order;
    }

    public function checkOrderStock($oId)
    {
        $oProducts = OrderProduct::where("order_id", "=", $oId)->select();
        $this->oProducts = $oProducts;
        $this->products = $this->getProductByOrder($oProducts);
        $status = $this->getOrderStatus();
        return $status;
    }

    private function createOrder($snap)
    {
        Db::startTrans();
        try {
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap["orderPrice"];
            $order->total_count = $snap["totalCount"];
            $order->snap_img = $snap["snapImg"];
            $order->snap_name = $snap["snapName"];
            $order->snap_address = $snap["snapAddress"];
            $order->snap_items = json_encode($snap["pStatus"]);
            $order->save();
            $orderId = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p) {
                $p["order_id"] = $orderId;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                "order_no" => $orderNo,
                "order_id" => $orderId,
                "create_time" => $create_time
            ];
        } catch (Exception $ex) {
            Db::rollback();
            throw $ex;
        }
    }

    public static function makeOrderNo()
    {
        $yCode = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J"];
        $orderSn = $yCode[intval(date("Y")) - 2018] . strtoupper(dechex(date("m"))) . date("d") . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf("%02d", rand(0, 9));
        return $orderSn;
    }

    private function snapOrder($status)
    {
        $snap = [
            "orderPrice" => 0,
            "totalCount" => 0,
            "pStatus" => [],
            "snapAddress" => null,
            "snapName" => "",
            "snapImg" => ""
        ];
        $snap["orderPrice"] = $status["orderPrice"];
        $snap["totalCount"] = $status["totalCount"];
        $snap["pStatus"] = $status["pStatusArray"];
        $snap["snapAddress"] = json_encode($this->getUserAddress());
        $snap["snapName"] = count($this->products) > 1 ? $this->products[0]["name"] . "等" : $this->products[0]["name"];
        $snap["snapImg"] = $this->products[0]["main_img_url"];
        return $snap;
    }

    private function getUserAddress()
    {
        $userAddress = UserAddress::where("user_id", "=", $this->uid)->find();
        if (!$userAddress) {
            throw new UserException([
                "msg" => "用户收货地址不存在，下单失败",
                "errorCode" => 60001
            ]);
        }
        return $userAddress->toArray();
    }

    private function getOrderStatus()
    {
        $status = [
            "pass" => true,
            "totalCount" => 0,
            "orderPrice" => 0,
            "pStatusArray" => []
        ];
        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($oProduct["product_id"], $oProduct["count"], $this->products);
            if (!$pStatus["haveStock"]) {
                $status["pass"] = false;
            }
            $status["totalCount"] += $pStatus["count"];
            $status["orderPrice"] += $pStatus["totalPrice"];
            array_push($status["pStatusArray"], $pStatus);
        }
        return $status;
    }

    private function getProductStatus($oPId, $oCount, $products)
    {
        $pStatus = [
            "id" => null,
            "haveStock" => false,
            "count" => 0,
            "name" => null,
            "totalPrice" => 0
        ];
        $pIndex = -1;
        for ($i = 0; $i < count($products); $i++) {
            if ($oPId == $products[$i]["id"]) {
                $pIndex = $i;
            }
        }
        if ($pIndex == -1) {
            throw new OrderException([
                "msg" => "ID为" . $oPId . "的商品不存在，创建订单失败"
            ]);
        } else {
            $product = $products[$pIndex];
            $pStatus["id"] = $product["id"];
            $pStatus["name"] = $product["name"];
            $pStatus["count"] = $oCount;
            $pStatus["totalPrice"] = $product["price"] * $oCount;
            $pStatus["haveStock"] = $product["stock"] > $oCount ? true : false;
        }
        return $pStatus;
    }

    private function getProductByOrder($oProducts)
    {
        $oPIds = [];
        foreach ($oProducts as $item) {
            array_push($oPIds, $item["product_id"]);
        }
        $products = Product::all($oPIds)->visible(["id", "price", "stock", "name", "main_img_url"])->toArray();
        return $products;
    }
}