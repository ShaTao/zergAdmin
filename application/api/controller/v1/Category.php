<?php
namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;

class Category
{
    public function getAllCategory()
    {
        $result = CategoryModel::all([], "img");
        if($result->isEmpty()){
            throw new CategoryException();
        }
        return json($result);
    }
}