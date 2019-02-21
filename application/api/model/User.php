<?php 

namespace app\api\model;

class User extends BaseModel
{
    protected $hidden = [ 'id','openid','update_time', 'delete_time', 'create_time'];
    public function address()
    {
        return $this->hasOne('UserAddress', 'user_id', 'id');
    }

    public static function getByOpenID($openid)
    {
        $user = self::where('openid', '=', $openid)->find();

        return $user;
    }


    public static function getUserInfoById($userId)
    {
        $user = self::where('id', '=', $userId)->find()->toArray();

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