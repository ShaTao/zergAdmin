<?php
namespace app\api\controller\v1;

use think\Exception;

use app\api\validate\IDMustBePositiveInt;

use app\api\model\Banner as BannerModel;
use app\api\model\BannerItem as BannerItemModel;
use app\lib\exception\BannerMissException;

class Banner
{
    function getBanner(IDMustBePositiveInt $idValidate)
    {
        $id = request()->param('id');        
        // (new IDMustBePositiveInt())->goCheck();
        $idValidate->goCheck();
        $banner = BannerModel::getBannerById($id);
        if (!$banner) {
            throw new BannerMissException();
        }
        // var_dump(config("self.img_url_prefix"));
        // $banner->hidden(["update_time","delete_time","items.update_time","items.delete_time"]);
        return json($banner);
    }
}
?>