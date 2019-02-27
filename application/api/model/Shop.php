<?php 

namespace app\api\model;

class Shop extends BaseModel
{
    public static function getShopInfo($userId)
    {
        $shopInfo = self::where(['user_id' => $userId])->find();
        if ($shopInfo != null)
            $shopInfo = $shopInfo->toArray();

        return $shopInfo;
    }
    public static function getShopInfoByShopId($shopId)
    {
        $shopInfo = self::where(['id' => $shopId])->find();
        if ($shopInfo != null)
            $shopInfo = $shopInfo->toArray();

        return $shopInfo;
    }

    public static function getShopInfoByUserId($userId)
    {
        $shopInfo = self::where(['user_id' => $userId])->find();
        if ($shopInfo != null)
            $shopInfo = $shopInfo->toArray();

        return $shopInfo;
    }

}