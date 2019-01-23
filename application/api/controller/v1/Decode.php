<?php

namespace app\api\controller\v1;
use \app\api\service\Decode as DecodeService;
use \app\api\service\Group as GroupService;
use \app\api\service\Token as TokenService;

class Decode
{
    protected $beforeActionList = [
        'checkDecodeData'
    ];


    public function decodeShare($encryptedData='', $iv='')
    {
        $result = DecodeService::getShareData($encryptedData, $iv);

        return $result;
    }

    public function decodeUser($encryptedData='', $iv='')
    {
        $result = DecodeService::getUserData($encryptedData, $iv);

        return $result;
    }

    public function getGroups()
    {
        $result = GroupService::getGroupsWithUser(TokenService::getCurrentUid());

        return $result;
    }
}