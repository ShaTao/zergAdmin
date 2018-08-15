<?php
namespace app\api\validate;

use think\Validate;

class TestValidate extends Validate
{
    protected $rule = [
        "name"=>"min:3|max:6",
        "email"=>"email"
    ];
}
?>