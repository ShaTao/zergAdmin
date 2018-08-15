<?php
namespace app\lib\exception;

class CategoryException extends BaseException
{
    public $code = 404;
    public $msg = "指定类目不存在，请检查ID";
    public $errorCode = 30000;
}