<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\OrderPlace;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\api\validate\PagingParameter;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\OrderException;


class Order extends BaseController
{
    protected $beforeActionList = [
        "needexclusivescope" => ["only" => "placeorder"]
    ];

    public function placeOrder()
    {
        (new OrderPlace())->goCheck();
        $uid = TokenService::getCurrentUid();
        $products = request()->param("products");
        $order = new OrderService();
        $status = $order->place($uid, $products);
        // $products = input("post.products/a");
        // $No = OrderService::makeOrderNo();
        // return $No;
        return json($status);
    }

    public function getSummaryByUser($page = 1, $size = 10)
    {
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUid();
        $pagingOrder = OrderModel::getSummaryByUser($uid, $page, $size);
        if (!$pagingOrder->empty()) {
            return [
                "data" => [],
                "current_page" => $pagingOrder->getCurrentPage()
            ];
        }
        return [
            "data" => $pagingOrder->hidden(["snap_item", "snap_address", "prepay_id"])->toArray(),
            "current_page" => $pagingOrder->getCurrentPage()
        ];
    }

    public function getOrderDetail($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        $result = $orderDetail->hidden(["prepay_id"]);
        return json($result);
    }
}