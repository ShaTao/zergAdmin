<?php
namespace app\lib\exception;

class WxChatException extends BaseException
{
    public $code = 400;
    public $msg = "";
    public $errorCode = 999;
}