<?php

namespace app\api\controller\v1;

use \app\api\validate\GroupUsersGet;
use app\api\model\Group as GroupModel;
use app\api\model\Commit as CommitModel;
use \app\api\service\Upload as UploadService;
use \app\api\service\Commit as CommitService;
use app\api\service\Token as TokenService;
use \think\Db;

class Group
{
    public function getGroupCommit($groupId='')
    {
        $result = CommitService::getGroupCommit($groupId);
        
        return $result;
    }

    public function getGroupUsers($groupId='')
    {
        // (new GroupUsersGet)->goCheck();
        $result = GroupModel::getGroupUsers($groupId);
        
        return $result;
    }

    public function userUploadSingle()
    {
        $result = UploadService::uploadPicture();

        return $result;
    }

    public function userUploadAdd()
    {
        $images = json_decode(input('post.images'), true);
        $content = input('post.content');
        $groupId = input('post.groupId');

        $userId = TokenService::getCurrentUid();
        $commitId = CommitService::createCommit($groupId, $userId, $content);

        CommitService::createCommitImage($commitId, $images);

        return array('error' => 0);
    }

    public function userUploadModify()
    {
        $images = json_decode(input('post.images'), true);
        $content = input('post.content');
        $commitId = input('post.commitId');

        CommitService::modifyCommitImage($content, $commitId, $images);

        return array('error' => 0);
    }

    public function userUploadDel()
    {
        $commitId = input('get.commitId');
        CommitService::deleteCommit($commitId);
    }

    public function groupUserCommit()
    {
        $groupId = input('get.groupId');
        $userId = TokenService::getCurrentUid();

        $commitList = CommitModel::getGroupUserCommit($groupId, $userId);
        return $commitList;
    }

    public function getCommit()
    {
        $commitId = input('get.commitId');
        $images =  CommitService::getCommit($commitId);
        return $images;
    }

}