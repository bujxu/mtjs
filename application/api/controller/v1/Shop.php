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
    public function getShopInfoByShopId()
    {
        $shopId = input('get.shop_id');
        $userId = TokenService::getCurrentUid();
        $result = ShopService::getShopInfoByShopId($shopId, $userId);
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