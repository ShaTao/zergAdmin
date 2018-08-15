<?php
namespace app\api\validate;

// use think\Validate;

use app\api\validate\BaseValidate;


class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        "id" => "require|isPositiveInt"
    ];

    protected $message = [
        "id" => "id必须是正整数"
    ];

    // protected function isPositiveInt($value, $rule = "", $data = "", $field = "")
    // {
    //     if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
    //         return true;
    //     } else {
    //         return $field . "必须是正整数！";
    //     }

    // }
}

?>