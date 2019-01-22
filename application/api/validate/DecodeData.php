<?php 

namespace app\api\validate;

class DecodeData extends BaseValidate
{
    protected $rule = [
        'encryptedData' => 'require|isNotEmpty',
        'iv' => 'require|isNotEmpty',
    ];

    protected $message = [
        'code' => '解码的参数不对'
    ];
}