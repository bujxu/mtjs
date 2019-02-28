<?php

namespace app\api\model;

class UserShop extends BaseModel
{
    public static function getUserIdByGoodId($GoodId)
    {
        $user = self::where(['good_id' => $GoodId])->find();
        return $user;
    }

    public static function getOtherShopId($userId)
    {
        $userShop = self::where(['user_id' => $userId])->select()->toArray();
        return array_column($userShop, 'shop_id');
    }

    public static function checkUserIdExist($GoodId, $userId)
    {
        $user = self::where(['good_id' => $GoodId, 'user_id' => $userId])->find();
        return $user;
    }

    public static function getGoodsByUserId($uid)
    {
        $goods = self::where(['user_id' => $uid])->select();
        return $goods;
    }

    public function goods()
    {
        return $this->belongsTo('Good', 'good_id', 'id');
    }
    
    public function users()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    
    public function UserId()
    {
        return $this->hasOne('UserGood', 'id', 'user_id');
    }

    public static function getGoodUsers($GoodId)
    {
        $users = self::where(['good_id' => $GoodId])->with(['UserId'])->select();
        return $users;
    }



}   