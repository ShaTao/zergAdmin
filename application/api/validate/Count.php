<?php
namespace app\api\validate;

class Count extends BaseValidate
{
    protected $rule = [
        "count" => "integer|between:1,20"
    ];
}