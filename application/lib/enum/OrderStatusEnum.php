<?php
namespace app\lib\enum;

class OrderStatusEnum
{
    //待支付
    const UPAID = 1;
    // 已支付
    const PAID = 1;
    // 已发货
    const DELIVERED = 3;
    // 已支付，但库存不足
    const PAID_BUT_STOCKOUT = 4;
}