<?php 

namespace app\api\model;
use traits\model\SoftDelete;
class Good extends BaseModel
{
    protected $hidden = ['update_time', 'delete_time', 'create_time'];
    protected $deleteTime = 'delete_time';

    use SoftDelete;
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

    public static function getGoodListDeleted($userId)
    {
        $goodList = self::onlyTrashed()->where(['user_id' => $userId])->with(['goodImages', 'goodImages.image'])->select()->toArray();

        return $goodList;
    }
    public static function getGoodInfo($id)
    {
        $goodInfo = self::withTrashed()->where(['id' => $id])->with(['goodImages', 'goodImages.image'])->find();
        if ($goodInfo == null)
        {
            return null;
        }
        return $goodInfo->toArray();
    }

}