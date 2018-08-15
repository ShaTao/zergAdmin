<?php
namespace app\api\model;

use think\Exception;
use think\Db;
// use think\Model;

class Banner extends BaseModel
{
    protected $hidden = ["delete_time", "update_time"];
    public static function getBannerById($id)
    {
        return self::with(["items", "items.image"])->find($id);
    }

    public function items()
    {
        return $this->hasMany("BannerItem", "banner_id", "id");
    }
}
?>