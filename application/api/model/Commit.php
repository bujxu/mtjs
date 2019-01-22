<?php 

namespace app\api\model;

class Commit extends BaseModel
{
    public function commitImages()
    {
        return $this->hasMany('commitImage', 'commit_id', 'id');
    }

    public static function getGroupUserCommit($groupId, $userId)
    {
        $commitList = self::where(['group_id' => $groupId, 'user_id' => $userId])->with(['commitImages', 'commitImages.image'])->select();
        return $commitList;
    }

    public static function getCommit($commitId)
    {
        $commit = self::where(['id' => $commitId])->with(['commitImages', 'commitImages.image'])->find()->toArray();

        return $commit;
    }

    public static function modifyContent($commitId, $content)
    {
        $commitDb = self::where(['id' => $commitId])->find();
        $commitDb->content = $content;
        $commitDb->save();
    }

    public static function getGroupCommit($groupId)
    {
        $offset = input('get.offset');
        $size = input('get.size');
        $commits = self::where(['group_id' => $groupId])->with(['commitImages', 'commitImages.image'])->limit($offset, $size)->select()->toArray();
        return $commits;
    }

    public static function getGroupNewestCommit($groupId)
    {
        $commit = self::where(['group_id' => $groupId])->order("id desc")->with(['commitImages', 'commitImages.image'])->find();
        if ($commit == null)
        {
            return null;
        }
 
        return $commit->toArray();
    }
}