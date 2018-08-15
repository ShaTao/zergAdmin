<?php
namespace app\api\controller\v1;

use app\api\validate\IDCollectionMustBeString;
use app\api\model\Theme as ThemeModel;
use app\lib\exception\ThemeException;
use app\api\validate\IDMustBePositiveInt;

class Theme
{
    /**
     * @url /theme?ids=id1,id2,id3,...
     * @return theme model group
     */
    public function getSimpleList($ids="")
    {
        (new IDCollectionMustBeString())->goCheck();
        $ids = explode(",", $ids);
        $result = ThemeModel::with(["topicImg", "headImg"])->select($ids);
        var_dump(empty([]));
        if($result->isEmpty()){
            throw new ThemeException();
        }
        return json($result);
    }

    /**
     * @url /theme/id
     * @return 
     */
    public function getComplexOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $result = ThemeModel::getThemeWithProduct($id);
        if(!$result){
            throw new ThemeException();
        }
        return json($result);
    }
}