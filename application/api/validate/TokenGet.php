<?php
namespace app\api\validate;

class TokenGet extends BaseValidate
{
    protected $rule = [
        "code" => "require|isNotEmpty"
    ];

    protected $message = [
        "code" => "code值为空，无法获得token"
    ];
}