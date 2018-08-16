<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\OrderPlace;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;


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
}