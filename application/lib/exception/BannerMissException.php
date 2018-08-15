<?php
namespace app\lib\exception;

use app\lib\exception\BaseException;

class BannerMissException extends BaseException
{
    public $code = 404;
    public $msg = "未找到该banner数据";
    public $errorCode = 40000;
}
?>