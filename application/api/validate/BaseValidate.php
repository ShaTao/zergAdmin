<?php
namespace app\api\validate;

use think\Validate;
use think\Exception;

use app\lib\exception\ParameterException;


class BaseValidate extends Validate
{
    public function goCheck()
    {
        $params = request()->param();
        $result = $this->batch()->check($params);
        if (!$result) {
            throw new ParameterException(["msg" => $this->error]);
        } else {
            return true;
        }
    }
    
    protected function isPositiveInt($value, $rule = "", $data = "", $field = "")
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            // return $field . "必须是正整数！";
            return false;
        }

    }

    protected function isNotEmpty($value, $rule = "", $data = "", $field = "")
    {
        if(empty($value)){
            return false;
        }else {
            return true;
        }
    }

    protected function isMobile($value, $rule = "", $data = "", $field = "")
    {
        $rule = "^1(3|4|5|7|8)[0-9]\d{8}$^";
        $result = preg_match($rule, $value);
        if($result){
            return true;
        }else {
            return false;
        }
    }

    public function getDataByRule($arr)
    {
        if(array_key_exists("user_id", $arr)|array_key_exists("uid", $arr)){
            throw new ParameterException([
                "msg"=>"参数中包含非法字段user_id或uid"
            ]);
        }
        $newArr = [];
        foreach($this->rule as $key => $value){
            $newArr[$key] = $arr[$key];
        }
        return $newArr;
    }
}