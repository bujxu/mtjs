<?php

namespace app\api\controller\v1;

use app\api\model\Shop as ShopModel;
use \app\api\service\Upload as UploadService;
use app\api\service\Token as TokenService;
use app\api\service\Shop as ShopService;
// use \think\Db;

class Shop
{
    public function getShopInfo()
    {
        $userId = TokenService::getCurrentUid();
        $result = ShopService::getShopInfo($userId);
        
        return $result;
    }

    public function getShopShareQCode()
    {
        $userId = TokenService::getCurrentUid();
        $result = ShopService::getShopShareQCode($userId);
        
        return $result;
    }

    public static function getMyShopInfo()
    {
        $userId = TokenService::getCurrentUid();
        $result = ShopService::getMyShopInfo($userId);
        
        return $result;
    }

    public static function getMyShopInfoDeleted()
    {
        $userId = TokenService::getCurrentUid();
        $result = ShopService::getMyShopInfoDeleted($userId);
        
        return $result;
    }
    public function getShopInfoByUserId()
    {
        $userId = TokenService::getCurrentUid();
        $result = ShopService::getShopInfoByUserId($userId);
        
        return $result;
    }

    public function getShopInfoByShopId()
    {
        $shopId = input('get.shop_id');

        $result = ShopService::getShopInfoByShopId($shopId);
        return $result;
    }
    
    public function getShopInfoByGoodId()
    {
        $goodId = input('get.good_id');

        $result = ShopService::getShopInfoByGoodId($goodId);
        return $result;
    }

    public function getShopListInfo()
    {
        $userId = TokenService::getCurrentUid();
        $result = ShopService::getShopListInfo($userId);
        
        return $result;
    }

    public function shopCreate()
    {
        $userId = TokenService::getCurrentUid();
        $content = input('post.');
        $result = ShopService::shopCreate($userId, $content);
        return $result;
    }

    
    public function shopEdit()
    {
        $content = input('post.');
        $result = ShopService::shopEdit($content);
        return $result;
    }
}