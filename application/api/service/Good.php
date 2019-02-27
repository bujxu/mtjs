<?php

namespace app\api\service;
use \think\Db;
use app\api\model\GoodImage as GoodImageModel;
use app\api\model\UserGood as UserGoodModel;
use app\api\model\UserShop as UserShopModel;
use app\api\model\UserAddress;
use app\api\model\Good as GoodModel;
use app\api\model\Shop as ShopModel;
use app\api\model\Image as ImageModel;
use app\api\service\Good as GoodService;
use \app\api\service\Token as TokenService;

class Good
{
    public static function createGood($userId, $content)
    {   
        Db::startTrans();
        try
        {
            $good = new GoodModel();
            $good_img = json_decode($content['good_img'], true);
            $good_img_detail = json_decode($content['good_img_detail'], true);
            $good->user_id = $userId;
            $good->good_name = $content['good_name'];
            $good->image_url = $good_img[0];
            $good->goodCategory = $content['goodCategory'];
            $good->good_desc = $content['good_desc'];
            $good->market_price = $content['market_price'];
            $good->good_stock = $content['good_stock'];
            $good->time = time();
    
            $good->save();

            self::createGoodImage($good->id, $good_img, 0);
            self::createGoodImage($good->id, $good_img_detail, 1);

            return $good->id;
        }
        catch (Exception $ex)
        {
            Db::rollback();
            throw $ex;
        }

    }

    public static function getImageUrl($val)
    {
        $imageUrl = [];
        foreach ($val as $value) {
            array_push($imageUrl,$value['image']['url']);
        }
        
        return $imageUrl;
    }

    public static function getImageUrls($val)
    {
        $imagesTemp = $val['good_images'];
        $imageId = [];
        $imageDetailId = [];
        foreach ($imagesTemp as $value) {
            if ($value["detail_image"] == 0)
            {
                array_push($imageId,$value['image']['url']);
            }
            else
            {
                array_push($imageDetailId,$value['image']['url']);
            }
        }
        
        return array($imageId, $imageDetailId);
    }


    public static function getGoodInfo($id)
    {
        $goodInfo = GoodModel::getGoodInfo($id);
        $goodInfo['imageUrl'] =  self::getImageUrls($goodInfo);
        $userId = TokenService::getCurrentUid();

        $address = UserAddress::where(['user_id' => $userId, 'status' => 'DEFAULT'])->find();
        if ($address != null)
        {
            $address = $address->toArray();
        }

        $shopInfo = ShopModel::get(['user_id' => $goodInfo['user_id']]);
        if ($userId != $goodInfo['user_id'])
        {
            $userShop = UserShopModel::where(['user_id' => $userId, 'shop_id' => $shopInfo->id])->find();
            if ($userShop == null && $shopInfo->id != 0)
            {
                $userShop = new UserShopModel();
                $userShop->user_id = $userId;
                $userShop->shop_id = $shopInfo->id;
                $userShop->save();
            }
        }

        return array('result' => 'ok', 'goodInfo' => $goodInfo, 'address' => $address, 'shopInfo' => $shopInfo->toArray());
    }

    public static function createGoodImage($goodId, $images, $detailImage)
    {   
        $imagesId = array_values($images);
        $length = count($imagesId);
        for ($index = 0; $index < $length; $index++)
        {
            $goodImage = new GoodImageModel();
        
            $goodImage->image_id = $imagesId[$index];
            $goodImage->good_id = $goodId;
            $goodImage->detail_image = $detailImage;
            $goodImage->save();
        }

        return ;
    }

    public static function deleteGoodImage($images)
    {
        $imagesId = array_values($images);
        $length = count($imagesId);
        for ($index = 0; $index < $length; $index++)
        {
            // ImageModel::destroy($imagesId[$index]);
            GoodImageModel::destroyGoodImageByImageId($imagesId[$index]);
        }
    }

    public static function getImageId($val)
    {
        $imagesTemp = $val['good_images'];
        $imageId = [];
        $imageDetailId = [];
        foreach ($imagesTemp as $value) {
            if ($value["detail_image"] == 0)
            {
                array_push($imageId,$value['image']['id']);
            }
            else
            {
                array_push($imageDetailId,$value['image']['id']);
            }
        }
        
        return array($imageId, $imageDetailId);
    }
    public static function userUploadModify($content)
    {   
        $imageIdNew = json_decode(input('post.good_img'), true);
        $imageIdDetailNew = json_decode(input('post.good_img_detail'), true);
        $goodImageIdOld = self::getImageId(GoodModel::getGoodImageId($content['good_id']));

        $addImageId = array_diff($imageIdNew, $goodImageIdOld[0]);
        $addImageDetailId = array_diff($imageIdDetailNew, $goodImageIdOld[1]);

        self::createGoodImage($content['good_id'], $addImageId, 0);
        self::createGoodImage($content['good_id'], $addImageDetailId, 1);

        $delImageId = array_diff($goodImageIdOld[0], $imageIdNew);
        $delImageDetailId = array_diff($goodImageIdOld[1], $imageIdDetailNew);
        self::deleteGoodImage($delImageId);
        self::deleteGoodImage($delImageDetailId);

        $goodDb = GoodModel::where(['id' => $content['good_id']])->find();
        $goodDb->good_name = $content['good_name'];
        $goodDb->good_desc = $content['good_desc'];
        $goodDb->goodCategory = $content['goodCategory'];
        $goodDb->market_price = $content['market_price'];
        $goodDb->good_stock = $content['good_stock'];

        $goodDb->save();
        return ;
    }
     
    public static function deleteGood($id)
    {
        GoodModel::destroy([$id]);
    }

    public static function editCommit($commitId)
    {
        
    }


    // public static function getCommit($commitId)
    // {
    //     $commit = CommitModel::getCommit($commitId);
    //     $images = array_column(array_column($commit['commit_images'], 'image'), 'url');
    //     $imagesId = array_column(array_column($commit['commit_images'], 'image'), 'id');
    //     $content = $commit['content'];

    //     return array('content' => $content, 'images' => $images, 'imagesId' => $imagesId);
    // }

    public static function getGoodByUserId($userId)
    {
        $goodList = GoodModel::getGoodList($userId);

        return $goodList;
    }


    public static function getGroupCommit($groupId)
    {
        $time = time();
        $commits = CommitModel::getGroupCommit($groupId);
        for ($index = 0; $index < count($commits); $index++)
        {
            $result[$index]['content'] = $commits[$index]['content'];
            $result[$index]['images'] = $commits[$index]['commit_images'][$time % (count($commits[$index]['commit_images']))]['image']['url'];
            $result[$index]['commitId'] = $commits[$index]['id'];
            $result[$index]['time'] = $commits[$index]['create_time'];
            $result[$index]['count'] = count($commits[$index]['commit_images']);
        }

        return $result;
    }
}