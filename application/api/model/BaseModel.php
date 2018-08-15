<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model
{
    protected $hidden = ["delete_time", "update_time", "create_time"];

    protected function imgUrlPrefix($value, $data)
    {
        $finalUrl = $value;
        if ($data["from"] == 1) {
            $finalUrl = config("self.img_url_prefix") . $finalUrl;
        }
        return $finalUrl;
    }
}
