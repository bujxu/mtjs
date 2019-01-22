<?php

namespace app\api\model;

class UserGroup extends BaseModel
{
    public static function getUserIdByGroupId($GroupId)
    {
        $user = self::where(['group_id' => $GroupId])->find();
        return $user;
    }

    public static function checkUserIdExist($groupId, $userId)
    {
        $user = self::where(['group_id' => $groupId, 'user_id' => $userId])->find();
        return $user;
    }

    public static function getGroupsByUserId($uid)
    {
        $groups = self::where(['user_id' => $uid])->select();
        return $groups;
    }

    public function groups()
    {
        return $this->belongsTo('Group', 'group_id', 'id');
    }
    
    public function users()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    
    public function UserId()
    {
        return $this->hasOne('UserGroup', 'id', 'user_id');
    }

    public static function getGroupUsers($groupId)
    {
        $users = self::where(['group_id' => $groupId])->with(['UserId'])->select();
        return $users;
    }

    // public function groups()
    // {
    //     return $this->belongsTo('Group', 'group_id', 'id');
    // }
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