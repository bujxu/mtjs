<?php

namespace app\api\service;
use \think\Db;
use \app\api\service\Token as TokenService;
use \app\api\model\User as UserModel;
use \app\api\model\Commit as CommitModel;
class Group
{
    public static function createGroup($openGId)
    {
        Db::startTrans();
        try
        {
            $group = new \app\api\model\Group();
            $group->openGId = $openGId;

            $group->save();

            Db::commit();
            return $group;
        }
        catch (Exception $ex)
        {
            Db::rollback();
            throw $ex;
        }
    }

    public static function createMap($groupId)
    {
        Db::startTrans();
        try
        {
            $userGroup = new \app\api\model\UserGroup();
            $userGroup->group_id = $groupId;
            $userGroup->user_id = TokenService::getCurrentUid();
            $userGroup->save();

            Db::commit();
            return $userGroup;
        }
        catch (Exception $ex)
        {
            Db::rollback();
            throw $ex;
        }
    }

    public static function getGroupNewestImage($groupId)
    {
        $commit = CommitModel::getGroupNewestCommit($groupId);
        
        return $commit['commit_images'][0]['image']['url'];
    }
    
    public static function getGroupsWithUser($groupId)
    {
        $groups = UserModel::getGroupsWithUser($groupId);
        $result = [];
        for ($index = 0; $index < count($groups); $index++)
        {
            $result[$index]['image'] = self::getGroupNewestImage($groups[$index]['group_id']);
            $result[$index]['groupId'] = $groups[$index]['group_id'];
            $result[$index]['openGId'] = $groups[$index]['groups']['openGId'];
        }

        return $result;
    }

}