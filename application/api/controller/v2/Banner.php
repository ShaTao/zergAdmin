<?php
namespace app\api\controller\v2;

use think\Request;
use think\Validate;

use app\api\validate\TestValidate;
use app\api\validate\IDMustBePositiveInt;


class Banner
{
    public function getBanner()
    {
        return "this is Version 2.0.0!";
    }

}
?>