<?php

namespace app\api\service;
use \think\Db;
use app\api\model\Shop as ShopModel;
use app\api\model\Image as ImageModel;
use app\api\model\Good as GoodModel;
use app\api\service\Good as GoodService;
use app\api\model\User as UserModel;
use app\api\model\UserShop as UserShopModel;

class Shop
{
    public static function shopCreate($userId, $content)
    {   
        Db::startTrans();
        try
        {
            $shop = new ShopModel();
            $shop->user_id = $userId;
            $shop->shop_name = $content['shop_name'];
            $shop->image_url = $content['image_url'];
            $shop->qr_code_image_url = $content['qr_code_image_url'];
            $shop->shop_desc = $content['shop_desc'];
            $shop->phone_number = $content['phone_number'];
            $shop->save();

            return $shop->id;
        }
        catch (Exception $ex)
        {
            Db::rollback();
            throw $ex;
        }

    }

    public static function shopEdit($content)
    {
        $shopDb = ShopModel::where(['id' => $content['shop_id']])->find();
        $shopDb->shop_name = $content['shop_name'];
        $shopDb->shop_desc = $content['shop_desc'];
        $shopDb->phone_number = $content['phone_number'];
        if (array_key_exists('image_url', $content))
        {
            $shopDb->image_url = $content['image_url'];
        }

        if (array_key_exists('qr_code_image_url', $content))
        {
            $shopDb->qr_code_image_url = $content['qr_code_image_url'];
        }

        $shopDb->save();
    }

    public static function getShopInfo($userId)
    {
        $shopInfo = ShopModel::getShopInfo($userId);
        if ($shopInfo != null)
        {
            // $map['id']  = ['id' => [['eq' , $shopInfo['image_url']], ['eq', $shopInfo['qr_code_image_url']], 'or']];
            $map['id']  = ['id' => ['eq' , $shopInfo['image_url']]];
            $image = ImageModel::where($map['id'])->find();
            if ($image != null)
            {
                $image = $image->toArray();
                $shopInfo['image_url'] = $image['url'];
            }

            $map['id']  = ['id' => ['eq' , $shopInfo['qr_code_image_url']]];
            $image = ImageModel::where($map['id'])->find();
            if ($image != null)
            {
                $image = $image->toArray();
                $shopInfo['qr_code_image_url'] = $image['url'];
            }
        }
        return $shopInfo;
    }

    public static function getImageUrl($val)
    {
        $imageUrl = [];
        foreach ($val as $value) {
            array_push($imageUrl,$value['image']['url']);
        }
        
        return $imageUrl;
    }

    public static function getShopInfoByShopId($shopId)
    {
        $shopInfo = ShopModel::getShopInfoByShopId($shopId);
        if ($shopInfo != null)
        {
            // $map['id']  = ['id' => [['eq' , $shopInfo['image_url']], ['eq', $shopInfo['qr_code_image_url']], 'or']];
            $map['id']  = ['id' => ['eq' , $shopInfo['image_url']]];
            $image = ImageModel::where($map['id'])->find();
            if ($image != null)
            {
                $image = $image->toArray();
                $shopInfo['image_url'] = $image['url'];
            }
            $userId = $shopInfo['user_id'];
            $goodList = GoodService::getGoodByUserId($userId);
            $imagesTemp = array_column($goodList, 'good_images');
    
            for ($index = 0; $index < count($imagesTemp); $index++) {
                $goodList[$index]['imageUrl'] =  self::getImageUrl($imagesTemp[$index]);
            }

            $userInfo = UserModel::getUserInfoById($userId);
        }

        return array('shopInfo' => $shopInfo, 'goodList' => $goodList, 'userInfo' => $userInfo);
    }

    public static function getShopInfoByGoodId($goodId)
    {
        $goodInfo = GoodModel::get(['id' => $goodId]);
        if ($goodInfo != null)
        {
            $user_id = $goodInfo->user_id;
            $shopInfo = ShopModel::getShopInfoByUserId($user_id);
            return self::getShopInfoByShopId($shopInfo['id'], $user_id);
        }

        return null;
    }

    public static function getShopGoodInfo($shopInfo)
    {
        if ($shopInfo != null)
        {
            // $map['id']  = ['id' => [['eq' , $shopInfo['image_url']], ['eq', $shopInfo['qr_code_image_url']], 'or']];
            $map['id']  = ['id' => ['eq' , $shopInfo['image_url']]];
            $image = ImageModel::where($map['id'])->find();
            if ($image != null)
            {
                $image = $image->toArray();
                $shopInfo['image_url'] = $image['url'];
            }

            $goodList = GoodService::getGoodByUserId($shopInfo['user_id']);
            $imagesTemp = array_column($goodList, 'good_images');
    
            for ($index = 0; $index < count($imagesTemp); $index++) {
                $goodList[$index]['imageUrl'] =  self::getImageUrl($imagesTemp[$index]);
            }
            return array('shopInfo' => $shopInfo, 'goodList' => $goodList);
        }
        return null;
    }

    public static function getShopListInfo($userId)
    {
        $shop = [];
        $otherUserIds = [0];
        array_push($otherUserIds, $userId);
        $otherUserId = UserShopModel::getOtherUsers($userId);
        if ($otherUserId != null)
        {
            $otherUserIds = array_merge($otherUserIds, $otherUserId);
        }

        for ($index = 0; $index < count($otherUserIds); $index++)
        {
            $shopInfo = ShopModel::getShopInfo($otherUserIds[$index]);
            $shopGoodInfo = self::getShopGoodInfo($shopInfo);
            if (null != $shopGoodInfo)
            {
                array_push($shop, $shopGoodInfo);
            }
        }

        return $shop;
    }
}