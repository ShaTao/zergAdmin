<?php
namespace app\api\controller\v1;

// use think\Collection;
use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\lib\exception\ProductException;
use app\api\validate\IDMustBePositiveInt;


class Product
{
    public function getRecentProduct($count=10)
    {
        (new Count())->goCheck();
        $products = ProductModel::getMostRecentProduct($count);
        if(!$products){
            return false;
            throw new ProductException();
        }
        // $collection = Collection::make($products);
        $result = $products->hidden(["summary", "from"]);
        return json($result);
    }

    public function getProductByCategory($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $result = ProductModel::getProductsByCategory($id);
        if($result->isEmpty()){
            throw new ProductException();
        }
        return json($result);
    }

    public function getOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $result = ProductModel::getProductDetail($id);
        if(!$result){
            throw new ProductException();
        }
        return json($result);
    }
}