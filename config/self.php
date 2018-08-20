<?php
return [
    //通用配置
    "img_url_prefix" => "http://z.com:8080/images",
    "token_expire" => 7200,
    //微信相关配置
    "app_id" => "wxbee6806016a521d2",
    "app_secret" => "3751b7a22df5a2a30791f6f64cc84dc1",
    "jscode_url" => "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
    //敏感信息配置
    "token_salt" => "HHb2Ued0qApTR69c",
    "pay_back_url" => "http://z.com/api/v1/notify"
];