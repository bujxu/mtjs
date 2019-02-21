<?php 

namespace app\api\model;

class GoodImage extends BaseModel
{
    protected $hidden = ['update_time', 'delete_time', 'create_time'];

    public function image()
    {
        return $this->belongsTo('Image', 'image_id', 'id');
    }
    
    public static function destroyGoodImageByImageId($imageId)
    {
        $goodImage = self::where(['image_id' => $imageId])->find()->toArray();
        self::destroy($goodImage['id']);
    }
}