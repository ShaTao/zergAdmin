<?php
namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\api\model\User as UserModel;
use app\lib\exception\UserException;
use app\lib\exception\SuccessMessage;
// use think\Controller;
// use app\lib\exception\TokenException;
// use app\lib\enum\ScopeEnum;
// use app\lib\exception\ForbiddenException;

class Address extends BaseController
{
    protected $beforeActionList = [
        "needprimaryscope" => ["only" => "createorupdateaddress"]
    ];

    public function createOrUpdateAddress()
    {
        $addressValidate = new AddressNew();
        $addressValidate->goCheck();
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if (!$user) {
            throw new UserException();
        }
        $dataArr = $addressValidate->getDataByRule(input("post."));
        $userAddress = $user->address;
        if (!$userAddress) {
            $user->address()->save($dataArr);
        } else {
            $user->address->save($dataArr);
        }
        return json(new SuccessMessage(), 201);
    }
}