<?php
/**
 * Created by 七月.
 * Author: 七月
 * Date: 2017/5/31
 * Time: 13:07
 */

namespace app\api\model;


use think\Paginator;

class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;

    public function image()
    {
        return $this->hasMany('Image', 'image_url', 'id');
    }

    public function getSnapItemsAttr($value)
    {
        if (empty($value))
        {
            return null;
        }
        return json_decode($value);
    }

    public function orderImages()
    {
        return $this->hasMany('orderImage', 'order_id', 'id');
    }


    public static function getOrderImageId($orderId)
    {
        $image = self::where(['id' => $orderId])->with(['orderImages', 'orderImages.image'])->find()->toArray();

        return $image;
    }
    public function getSnapAddressAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public static function getSummaryByUser($uid, $page = 1, $size = 15)
    {
        $pagingData = self::where('user_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData;
    }

    public static function getSummaryByPage($page=1, $size=20){
        $pagingData = self::order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }
}