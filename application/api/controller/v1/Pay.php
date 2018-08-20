<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\service\Pay as PayService;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\WxNotify;


class Pay extends BaseController
{
    public function advancePay($id='')
    {
        (new IDMustBePositiveInt())->goCheck();
        $result = (new PayService($id))->pay();
        return json($result);
    }

    public function receiveNotify()
    {
        $notify = new WxNotify();
        $config = new \WxPayConfig();
        $notify->Handle($config);
    }
}