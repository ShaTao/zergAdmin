<?php
namespace app\api\service;

use app\lib\exception\TokenException;
// use think\facade\Cache;


class Token
{
    public static function generateToken()
    {
        $randChars = getRandChars(32);
        $timestamp = $_SERVER["REQUEST_TIME_FLOAT"];
        $salt = config("self.token_salt");
        return md5($randChars . $timestamp . $salt);
    }

    public static function getCurrentTokenVar($key)
    {
        $token = request()->header("token");
        $vars = cache($token);
        if (!$vars) {
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new TokenException([
                    "msg" => "尝试获取的token变量不存在"
                ]);
            }

        }
    }

    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar("uid");
        return $uid;
    }
}