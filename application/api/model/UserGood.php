<?php

namespace app\api\model;

class UserGood extends BaseModel
{
    public static function getUserIdByGoodId($GoodId)
    {
        $user = self::where(['good_id' => $GoodId])->find();
        return $user;
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

    // public function groups()
    // {
    //     return $this->belongsTo('Group', 'good_id', 'id');
    // }i
    // public function UserGroup()
    // {
    //     return $this->hasMany('UserGroup', 'user_id', 'id');
    // }
    // public static function getGroupsWithUser($id)
    // {
    //     $groups = self::where(['id' => $id])->with(['UserGroup', 'UserGroup.groups'])->select();
    //     return $groups;
    // }

}   