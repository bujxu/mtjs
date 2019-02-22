<?php 

namespace app\api\model;

class Good extends BaseModel
{
    protected $hidden = ['user_id', 'update_time', 'delete_time', 'create_time'];

    public function goodImages()
    {
        return $this->hasMany('goodImage', 'good_id', 'id');
    }

    public static function getGroupUserCommit($groupId, $userId)
    {
        $commitList = self::where(['group_id' => $groupId, 'user_id' => $userId])->with(['commitImages', 'commitImages.image'])->select();
        return $commitList;
    }

    public static function getGoodImageId($goodId)
    {
        $image = self::where(['id' => $goodId])->with(['goodImages', 'goodImages.image'])->find()->toArray();

        return $image;
    }

    public static function getGoodList($userId)
    {
        $goodList = self::where(['user_id' => $userId])->with(['goodImages', 'goodImages.image'])->select()->toArray();

        return $goodList;
    }

    public static function getGoodInfo($id)
    {
        $goodInfo = self::where(['id' => $id])->with(['goodImages', 'goodImages.image'])->find();
        if ($goodInfo == null)
        {
            return null;
        }
        return $goodInfo->toArray();
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