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
            
            // $userShop = new UserShopModel();
            // $userShop->user_id = $userId;
            // $userShop->shop_id = $shop->id;
            // $userShop->save();

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

    
    protected static function _requestGet($url, $ssl=true) {
        // curl完成
        $curl = curl_init();

        //设置curl选项
        curl_setopt($curl, CURLOPT_URL, $url);//URL
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '
Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
        curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);//referer头，请求来源
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);//设置超时时间

        //SSL相关
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//禁用后cURL将终止从服务端进行验证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//检查服务器SSL证书中是否存在一个公用名(common name)。
        }
        curl_setopt($curl, CURLOPT_HEADER, false);//是否处理响应头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//curl_exec()是否返回响应结果

        // 发出请求
        $response = curl_exec($curl);
        if (false === $response) {
            echo '<br>', curl_error($curl), '<br>';
            return false;
        }
        curl_close($curl);
        return $response;
    }

     protected static function _requestPost($url, $data, $ssl=true) {
            //curl完成
            $curl = curl_init();
            //设置curl选项
            curl_setopt($curl, CURLOPT_URL, $url);//URL
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '
    Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
            curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);//user_agent，请求代理信息
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);//referer头，请求来源
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);//设置超时时间
            //SSL相关
            if ($ssl) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//禁用后cURL将终止从服务端进行验证
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//检查服务器SSL证书中是否存在一个公用名(common name)。
            }
            // 处理post相关选项
            curl_setopt($curl, CURLOPT_POST, true);// 是否为POST请求
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);// 处理请求数据
            // 处理响应结果
            curl_setopt($curl, CURLOPT_HEADER, false);//是否处理响应头
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//curl_exec()是否返回响应结果

            // 发出请求
            $response = curl_exec($curl);
            if (false === $response) {
                echo '<br>', curl_error($curl), '<br>';
                return false;
            }
            curl_close($curl);
            return $response;
    }

    
    public static function _getAccessToken() {

        // 考虑过期问题，将获取的access_token存储到某个文件中

        // 目标URL：        
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".config('wx.app_id')."&secret=".config('wx.app_secret');
        //向该URL，发送GET请求
        $result = self::_requestGet($url);
        if (!$result) {
            return false;
        }
        // 存在返回响应结果
        $result_obj = json_decode($result);

        return $result_obj->access_token;
    }


    public static function getShopShareQCode($userId)
    {
        $shop = ShopModel::get(['user_id' => $userId]);
        if ($shop == null)
        {
            return;
        }
        $access_token = self::_getAccessToken();

        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token;

        $data = array();  

        $data['scene'] = 'shop'.$shop->id;//自定义信息，可以填写诸如识别用户身份的字段，注意用中文时的情况  
        $data['page'] = 'pages/shop/shopShow/shopShow';//扫描后对应的path  
        $fileName = 'shop'.$shop->id;

        $upload_config = Config('setting.upload_config');
        $savePath = $upload_config['savePath'];
   
        $data['width'] = 400;//自定义的尺寸  
        $data['auto_color'] = false;//是否自定义颜色  
        $color = array(  
            "r"=>"221",  
            "g"=>"0",  
            "b"=>"0",  
        );  
        $data['line_color'] = $color;//自定义的颜色值  
        $data = json_encode($data); 
        $result = self::_requestPost($url,$data);  
 
        $QRcodePath = $savePath."QRcode/".$fileName.".jpg";
        
        $ret['result'] = file_put_contents($QRcodePath,$result);			//	将获取到的二维码图片流保存成图片文件
        if ($ret['result'] != false)
        {
            $ret['result'] = 0;
        }
        else
        {
            $ret['result'] = 1;
        }
        $QRcodePath = Config('setting.web_url').Config('setting.QRcode').$fileName.".jpg";
        $ret['filePath'] = $QRcodePath;

        echo json_encode($ret);
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

    public static function getShopInfoByUserId($user_id)
    {
        $shopInfo = ShopModel::getShopInfoByUserId($user_id);
        if ($shopInfo == null)
        {
            return null;
        }
        return self::getShopInfoByShopId($shopInfo['id'], $user_id);
    }

    public static function getMyShopInfo($userId)
    {
        $shopInfo = ShopModel::get(['user_id' => $userId]);
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
            $goodList = GoodService::getGoodByUserId($userId);
            // $imagesTemp = array_column($goodList, 'good_images');
    
            for ($index = 0; $index < count($goodList); $index++) {
                // array_push($goodList[$index], self::getImageUrl($imagesTemp[$index]));
                $goodList[$index]['imageUrl'] =  GoodService::getImageUrls($goodList[$index]);
                $goodList[$index]['imageId'] = GoodService::getImageId($goodList[$index]);
                // $goodList[$index]['goodCategory'] = unserialize($goodList[$index]['goodCategory']);
            }
            $userInfo = UserModel::getUserInfoById($shopInfo['user_id']);
            return array('shopInfo' => $shopInfo, 'goodList' => $goodList, 'userInfo' => $userInfo);
        }
        
        return null;

    }

    public static function getMyShopInfoDeleted($userId)
    {
        $shopInfo = ShopModel::get(['user_id' => $userId]);
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
            $goodList = GoodService::getGoodByUserIdDeleted($userId);
            // $imagesTemp = array_column($goodList, 'good_images');
    
            for ($index = 0; $index < count($goodList); $index++) {
                // array_push($goodList[$index], self::getImageUrl($imagesTemp[$index]));
                $goodList[$index]['imageUrl'] =  GoodService::getImageUrls($goodList[$index]);
                $goodList[$index]['imageId'] = GoodService::getImageId($goodList[$index]);
                // $goodList[$index]['goodCategory'] = unserialize($goodList[$index]['goodCategory']);
            }
            $userInfo = UserModel::getUserInfoById($shopInfo['user_id']);
            return array('shopInfo' => $shopInfo, 'goodList' => $goodList, 'userInfo' => $userInfo);
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
            $userInfo = UserModel::getUserInfoById($shopInfo['user_id']);
            return array('shopInfo' => $shopInfo, 'goodList' => $goodList, 'userInfo' => $userInfo);
        }
        return null;
    }
    
    public static function getShopListInfo($userId)
    {
        $shop = [];
        $shopId = [0];
        $myShop = ShopModel::where(['user_id' => $userId])->find();
        if ($myShop != null)
        {
            array_push($shopId, $myShop->id);
        }
        $otherShopId = UserShopModel::getOtherShopId($userId);
        if ($otherShopId != null)
        {
            $shopId = array_merge($shopId, $otherShopId);
        }

        for ($index = 0; $index < count($shopId); $index++)
        {
            $shopInfo = ShopModel::getShopInfoByShopId($shopId[$index]);
            $shopGoodInfo = self::getShopGoodInfo($shopInfo);
            if (null != $shopGoodInfo)
            {
                array_push($shop, $shopGoodInfo);
            }
        }

        return $shop;
    }
}