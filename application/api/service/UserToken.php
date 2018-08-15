<?php
namespace app\api\service;

use think\Exception;
use app\lib\exception\WxChatException;
use app\api\model\User as UserModel;
use app\lib\exception\TokenException;

class UserToken extends Token
{
    protected $code;
    protected $wxAppId;
    protected $wxAppSecret;
    protected $wxJsCodeUrl;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppId = config("self.app_id");
        $this->wxAppSecret = config("self.app_secret");
        $this->wxJsCodeUrl = sprintf(config("self.jscode_url"), $this->wxAppId, $this->wxAppSecret, $this->code);
        // echo $this->wxJsCodeUrl;
    }

    public function get()
    {
        $wxResult = curl_get($this->wxJsCodeUrl);
        $result = json_decode($wxResult, true);
        if (empty($result)) {
            throw new Exception("获取session_key,open_id异常,微信内部错误");
        } else {
            $loginFail = key_exists("errcode", $result);
            if ($loginFail) {
                // throw new Exception("获取session_key,open_id异常,微信内部错误");
                $this->processLoginError($result);
            } else {
                // return json($result);
                $token = $this->grantToken($result);
                return $token;
            }
        }
    }

    private function grantToken($result)
    {
        $openid = $result["openid"];
        $user = UserModel::getUserByOpenId($openid);
        if ($user) {
            $uid = $user->id;
        } else {
            $uid = $this->newUser($openid);
        }
        $cacheValue = $this->prepareCacheValue($result, $uid);
        $token = $this->saveToCache($cacheValue);
        return $token;
    }

    private function saveToCache($cacheValue)
    {
        $key = self::generateToken();
        $value = json_encode($cacheValue);
        $expire_in = config("self.token_expire");
        $result = cache($key, $value, $expire_in);
        if(!$result){
            throw new TokenException([
                "msg" => "服务器缓存异常",
                "errorCode" => 10005
            ]);
        }
        return $key;
    }

    private function prepareCacheValue($result, $uid)
    {
        $cacheValue = $result;
        $cacheValue["uid"] = $uid;
        $cacheValue["scope"] = 16;
        return $cacheValue;
    }

    private function newUser($openid)
    {
        $user = UserModel::create([
            "openid" => $openid
        ]);
        return $user->id;
    }

    private function processLoginError($result)
    {
        throw new WxChatException([
            "msg" => $result["errmsg"],
            "errorCode" => $result["errcode"]
        ]);
    }
}