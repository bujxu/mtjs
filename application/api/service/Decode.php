<?php 

namespace app\api\service;
use \app\api\service\Token as TokenService;
use \app\lib\share\WXBizDataCrypt;
use app\api\model\Group as GroupModel;
use app\api\model\UserGroup as UserGroupModel;
use app\api\service\Group as GroupService;
use app\api\model\User as UserModel;

class Decode
{
    public static function decodeData($encryptedData, $iv)
    {
        $result = [];
        $sessionKey = TokenService::getCurrentTokenVar('session_key');
        $pc = new WXBizDataCrypt(config('wx.app_id'), $sessionKey);
        $result['errCode'] = $pc->decryptData($encryptedData, $iv, $data);
        $result['data'] = $data;

        return $result;
    }

    public static function getShareData($encryptedData, $iv)
    {
        $data = array();
        $result = self::decodeData($encryptedData ,$iv);

        if ($result['errCode'] == 0) 
        {
            $data = json_decode($result['data'], true);
            $group = GroupModel::getGroupIdByOpenGId($data['openGId']);
            if (!$group['id'])
            {
                $group = GroupService::createGroup($data['openGId']);
                $userGroup = GroupService::createMap($group['id']);
            }
            else 
            {
                // $user = UserGroupModel::getUserIdByGroupId($group['id']);
                $user = UserGroupModel::checkUserIdExist($group['id'], TokenService::getCurrentUid());
                if (!$user['user_id'])
                {
                    $userGroup = GroupService::createMap($group['id']);
                }
            }
            return $data;
        } 

        return $result['errCode'];
    }

    public static function getUserData($encryptedData, $iv)
    {
        $result = self::decodeData($encryptedData ,$iv);

        if ($result['errCode'] == 0) 
        {
            $data = json_decode($result['data'], true);
            $user = UserModel::getByOpenID($data['openId']);
 
            $user->nickname = $data['nickName'];
            $user->avatarUrl = $data['avatarUrl'];
            $user->province = $data['province'];
            $user->city = $data['city'];
            $user->gender = $data['gender'];
            $user->save();

            return $data;
        } 
        
        return $result['errCode'];
    }
}