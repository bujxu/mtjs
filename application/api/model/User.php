<?php 

namespace app\api\model;

class User extends BaseModel
{
    public function address()
    {
        return $this->hasOne('UserAddress', 'user_id', 'id');
    }

    public static function getByOpenID($openid)
    {
        $user = self::where('openid', '=', $openid)->find();

        return $user;
    }

    public function UserGroup()
    {
        return $this->hasMany('UserGroup', 'user_id', 'id');
    }
    
    public static function getGroupsWithUser($id)
    {
        $groups = self::where(['id' => $id])->with(['UserGroup', 'UserGroup.groups'])->select()->toArray();
        return $groups[0]['user_group'];
    }
    

}