<?php 

namespace app\api\model;

class OrderImage extends BaseModel
{
    protected $hidden = ['update_time', 'delete_time', 'create_time'];

    public function image()
    {
        return $this->belongsTo('Image', 'image_id', 'id');
    }
    
    public static function destroyOrderImageByImageId($imageId)
    {
        $orderImage = self::where(['image_id' => $imageId])->find()->toArray();
        self::destroy($orderImage['id']);
    }
}