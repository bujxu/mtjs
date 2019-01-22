<?php
namespace app\api\model;


class Group extends BaseModel
{
    // public function getUrlAttr($value, $data)
    // {
    //     return $this->prefixImgUrl($value, $data);
    // }

    // public static function getGroupsByUserID($userID)
    // {
    //     $products = self::where('category_id', '=', $categoryID)->select();
    //     return $products;
    // }

    public static function getGroupIdByOpenGId($openGId)
    {
        $group = self::where(['openGId' => $openGId])->find();
        return $group;
    }

    public function UserGroup()
    {
        return $this->hasMany('UserGroup', 'group_id', 'id');
    }

    public static function getGroupUsers($groupId)
    {
        $users = self::where(['id' => $groupId])->with(['UserGroup', 'UserGroup.users'])->select();
        return $users;
    }

    // public static function getNewestImage($groupId)
    // {
    //     $group = self::where(['id' => $groupId])->find()->toArray();
    //     return $group['image'];
    // }
}