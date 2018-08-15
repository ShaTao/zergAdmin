<?php
namespace app\api\model;

class Product extends BaseModel
{
    protected $hidden = ["delete_time", "update_time", "create_time", "pivot"];

    public function getMainImgUrlAttr($value, $data)
    {
        return $this->imgUrlPrefix($value, $data);
    }

    public function imgs()
    {
        // return $this->hasMany("ProductImage", "product_id", "id")->order("order", "asc");
        return $this->hasMany("ProductImage", "product_id", "id");
    }

    public function properties()
    {
        return $this->hasMany("ProductProperty", "product_id", "id");
    }

    public static function getMostRecentProduct($count)
    {
        $result = self::limit($count)->order("create_time desc")->select();
        return $result;
    }

    public static function getProductsByCategory($id)
    {
        $result = self::where("category_id", "=", $id)->select();
        return $result;
    }

    public static function getProductDetail($id)
    {
        // $result = self::with(["imgs.imgUrl", "properties"])->find($id);
        $result = self::with(["imgs"=>function($query){
            $query->with(["imgUrl"])->order("order", "asc");
        }])->append(["properties"])->find($id);
        return $result;
    }
}