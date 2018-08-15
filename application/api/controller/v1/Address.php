<?php
namespace app\api\controller\v1;

use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\api\model\User as UserModel;
use app\lib\exception\UserException;
use app\lib\exception\SuccessMessage;

class Address
{
    public function createOrUpdateAddress()
    {
        $addressValidate = new AddressNew();
        $addressValidate->goCheck();
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }
        // return $uid;
        $dataArr = $addressValidate->getDataByRule(input("post."));
        $userAddress = $user->address;
        if(!$userAddress){
            $user->address()->save($dataArr);
        }else{
            $user->address->save($dataArr);
        }
        // return json($user);
        return json(new SuccessMessage(), 201);
    }
}