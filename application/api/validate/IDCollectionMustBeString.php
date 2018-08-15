<?php
namespace app\api\validate;

class IDCollectionMustBeString extends BaseValidate
{
    protected $rule = [
        "ids" => "require|checkIds"
    ];

    protected $message = [
        "ids" => "ids参数必须是以逗号分隔的多个正整数"
    ];

    protected function checkIds($value)
    {
        $values = explode(",", $value);
        if(empty($values)){
            return false;
        }
        foreach($values as $id){
            if(!$this->isPositiveInt($id)){
                return false;
            }
        }
        return true;
    }
}