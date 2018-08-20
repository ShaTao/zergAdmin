<?php
namespace app\lib\exception;

class WxChatException extends BaseException
{
    public $code = 400;
    public $msg = "未知的微信错误";
    public $errorCode = 999;
}