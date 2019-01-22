<?php 

namespace app\api\model;

class CommitImage extends BaseModel
{

    public function image()
    {
        return $this->belongsTo('Image', 'image_id', 'id');
    }
    
    public static function destroyCommitImageByImageId($imageId)
    {
        $commitImage = self::where(['image_id' => $imageId])->find()->toArray();
        self::destroy($commitImage['id']);
    }
}