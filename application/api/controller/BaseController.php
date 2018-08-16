<?php
namespace app\api\controller;

use think\Controller;
use app\api\service\Token as TokenService;
use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;


class BaseController extends Controller
{
    protected function needPrimaryScope()
    {
        $scope = TokenService::getCurrentTokenVar("scope");
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    protected function needExclusiveScope()
    {
        $scope = TokenService::getCurrentTokenVar("scope");
        if ($scope) {
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException([
                    "msg" => "权限不符"
                ]);
            }
        } else {
            throw new TokenException();
        }
    }
}