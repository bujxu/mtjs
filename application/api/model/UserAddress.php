<?php

namespace app\api\model;

class UserAddress extends BaseModel
{
    protected $hidden = [
        'id', 'delete_time', 'user_id', 'create_time', 'update_time', 'who'
    ];

    // public static function getAddress($uid, $who)
    // {
    //     $address = self::where(['user_id' => $uid, 'who' => $who])->select();
    //     return $address;
    // }

}   