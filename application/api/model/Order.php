<?php
namespace app\api\model;

class Order extends BaseModel
{
    protected $hidden = ["user_id", "delete_time", "update_time"];

    protected $autoWriteTimestamp = true;

    protected function getSummaryByUser($uid, $page, $size)
    {
        $pagingDate = self::where("user_id", "=", $uid)->order("create_time desc")->paginate($size, true, ["page"=>$page]);
        return $pagingDate;
    }

    public function getSnapItemsAttr($value)
    {
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value)
    {
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }
}