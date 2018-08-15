<?php
namespace app\lib\exception;

use Exception;
use think\exception\HttpException;
use think\exception\Handle;
use think\Request;

use app\lib\exception\BaseException;

class ExceptionHandle extends Handle
{
    private $code;
    private $errorCode;
    private $msg;

    public function render(Exception $ex)
    {
        if ($ex instanceof BaseException) {
            $this->code = $ex->code;
            $this->msg = $ex->msg;
            $this->errorCode = $ex->errorCode;
        } else if ($ex instanceof HttpException) {
            $this->code = $ex->getStatusCode();
            $this->msg = $ex->getMessage();
            $this->errorCode = "10001";
        } else {
            $this->code = 500;
            $this->msg = "服务器错误:".$ex->getMessage();
            $this->errorCode = 50001;
        }
        $result = [
            "errorCode" => $this->errorCode,
            "msg" => $this->msg,
            "requestUrl" => request()->url(true)
        ];
        return json($result, $this->code);
    }
}
?>