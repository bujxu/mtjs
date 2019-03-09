<?php

namespace app\api\service;
use \think\Db;
use app\api\model\UserAddress;
use app\api\model\Order as OrderModel;
use app\api\model\OrderImage as OrderImageModel;
use app\api\model\Good as GoodModel;
use app\api\model\Image as ImageModel;
use app\api\service\Shop as ShopService;

class Order
{
    public $input;
    public $products;
    public $uid;

    public function place($uid, $input)
    {
        $this->input = $input;
        $this->uid = $uid;
        // $this->products = $this->getProductsByOrder($input);
        // $status = $this->getOrderStatus();
        // if (!$status['pass'])
        // {
        //     $status['order_id'] = -1;
        //     return $status;
        // }

        // $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder();
        return $order;
    }

    public function createOrder()
    {
        Db::startTrans();
        try
        {
            $orderNo = $this->makeOrderNo();
            $order = new \app\api\model\Order();
            $address = UserAddress::get(['id' => $this->input['address_id']]);
            $good = GoodModel::get(['id' => $this->input['good_id']]);

            $order->image_url = $good->image_url;
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->totalPrice = $this->input['totalPrice'];
            $order->address_detail = $address->address_detail;
            $order->house_number = $address->house_number;
            $order->contact = $address->contact;
            $order->phone_number = $address->phone_number;
            $order->goodSpecification = $this->input['goodSpecification'];
            $order->good_id =  $this->input['good_id'];
            $order->good_owner_id = $good->user_id;
            $order->goodCount = $this->input['goodCount'];
            $order->good_name = $good->good_name;
            $order->good_desc = $good->good_desc;
            $order->goodCategory = $good->goodCategory;
            $order->image_url = $good->image_url;
            $order->status = 0;
            $order->save();

            $orderID = $order->id;
            $create_time = $order->create_time;

            // foreach ($this->input as &$p)
            // {
            //     $p['order_id'] = $orderID;
            // }
            // $orderProduct = new \app\api\model\OrderProduct();
            // $orderProduct->saveAll($this->input);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time,
                'result' => 'ok'
            ];
        }
        catch (Exception $ex)
        {
            Db::rollback();
            throw $ex;
        }
    }

    public static function orderDelete($orderId)
    {
        $imagesId = self::getImageId(OrderModel::getOrderImageId($orderId));
        $length = count($imagesId);
        for ($index = 0; $index < $length; $index++)
        {
            ImageModel::destroy($imagesId[$index]);
            OrderImageModel::destroyOrderImageByImageId($imagesId[$index]);
        }
        
        orderModel::destroy([$orderId]);
    }

    public static function orderStatus($input, $uid)
    {
        $order = orderModel::get(['id' => $input['id']]);
        if (!$order)
        {
            return ['result' => 'failed'];
        }
        
        if ($input['status'] == 1)
        {
            $goodCount = $input['goodCount'];
            $goodCategory = $input['goodCategory'];

            $lenght = count($goodCount);
            for ($index = 0; $index < $lenght; $index++)
            {
                $goodCategory[$index]['storageInput'] -= $goodCount[$index];
            }
            $good = GoodModel::get(['id' => $order->good_id]);
            $good->goodCategory = json_encode($goodCategory);
            $good->save();
        }

        $shopInfo = ShopService::getShopInfo($order->good_owner_id);
        $order->status = $input['status'];
        $order->save();

        return ['result' => 'ok', 'shopInfo' => $shopInfo];
    }

    public static function createOrderImage($orderId, $images)
    {   
        $imagesId = array_values($images);
        $length = count($imagesId);
        for ($index = 0; $index < $length; $index++)
        {
            $orderImage = new OrderImageModel();
            $orderImage->image_id = $imagesId[$index];
            $orderImage->order_id = $orderId;
            $orderImage->save();
        }

        return ;
    }

    public static function getImageId($val)
    {
        $imagesTemp = $val['order_images'];
        $imageId = [];
        foreach ($imagesTemp as $value) {
            array_push($imageId,$value['image']['id']);
        }
        
        return $imageId;
    }

    public static function userUploadModify($content)
    {   
        $imageIdNew = json_decode(input('post.order_img'), true);
        $orderImageIdOld = self::getImageId(OrderModel::getOrderImageId($content['order_id']));
        $addImageId = array_diff($imageIdNew, $orderImageIdOld);
        self::createOrderImage($content['order_id'], $addImageId);
        $delImageId = array_diff($orderImageIdOld, $imageIdNew);
        self::deleteOrderImage($delImageId);

        $order = OrderModel::get(['id' => $content['order_id']]);
        if ($order)
        {
            if ($order->status == 1)
            {
                $order->status += 1;
                $order->save();
            }
        }
        return ;
    }

    public static function deleteOrderImage($images)
    {
        $imagesId = array_values($images);
        $length = count($imagesId);
        for ($index = 0; $index < $length; $index++)
        {
            ImageModel::destroy($imagesId[$index]);
            OrderImageModel::destroyOrderImageByImageId($imagesId[$index]);
        }
    }

    public static function getImageUrls($val)
    {
        $imagesTemp = $val['order_images'];
        $imageId = [];
        foreach ($imagesTemp as $value) {
            array_push($imageId,$value['image']['url']);
        }
        
        return $imageId;
    }

    public static function getOrderDetail($id)
    {
        // $id = input('get.order_id');
        $order = orderModel::get(['id' => $id]);
        if (!$order)
        {
            return ['result' => 'failed'];
        }
        
        if ($order->status > 1)
        {
            $order['orderImageUrl'] =  self::getImageUrls($order);
        }

        $image = ImageModel::get(['id' => $order->image_url]);
        // $order = $order->toArray();
        $order['image_url'] = $image->url;

        return ['result' => 'ok', 'order' => $order->toArray()];
    }

    public static function getOrder($uid)
    {
        $status = input('get.status');
        if ($status == 10)
            $result = OrderModel::where(['user_id' => $uid])->where('status', 'neq', 4)->select()->toArray();
        else
            $result = OrderModel::where(['user_id' => $uid, 'status' => $status])->select()->toArray();
        
        for ($index = 0; $index < count($result); $index++)
        {
            if ($result[$index]['image_url'] != null)
            {
                $image = ImageModel::get(['id' => $result[$index]['image_url']]);
                if ($image != null)
                {
                    $result[$index]['image_url'] = $image->url;
                }

            }

            $result[$index]['goodCategory'] = json_decode($result[$index]['goodCategory'], true);
            $result[$index]['goodCount'] = json_decode($result[$index]['goodCount'], true);
        }
        
        return $result;
    }

    public static function getOrderBySellerId($uid)
    {
        $status = input('get.status');
        if ($status == 10)
            $result = OrderModel::where(['good_owner_id' => $uid])->select()->toArray();
        else
            $result = OrderModel::where(['good_owner_id' => $uid, 'status' => $status])->select()->toArray();
        
        for ($index = 0; $index < count($result); $index++)
        {
            if ($result[$index]['image_url'] != null)
            {
                $image = ImageModel::get(['id' => $result[$index]['image_url']]);
                // array_push($result[$index], ['image' => $image->url]);
                $result[$index]['image_url'] = $image->url;
            }

            $result[$index]['goodCategory'] = json_decode($result[$index]['goodCategory'], true);
            $result[$index]['goodCount'] = json_decode($result[$index]['goodCount'], true);
        }
        
        return $result;
    }
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    // public function snapOrder($status)
    // {
    //     $snap = [
    //         'orderPrice' => 0,
    //         'totalCount' => 0,
    //         'pStatus' => [],
    //         'snapAddress' => null,
    //         'snapName' => '',
    //         'snapImg' => '',
    //     ];

    //     $snap['orderPrice'] = $status['orderPrice'];
    //     $snap['totalCount'] = $status['totalCount'];
    //     $snap['pStatus'] = $status['pStatusArray'];
    //     $snap['snapAddress'] = \json_encode($this->getUserAddress());
    //     $snap['snapName'] = $this->products[0]['name'];
    //     $snap['snapImg'] = $this->products[0]['main_img_url'];

    //     return $snap;
    // }



    public function getUserAddress()
    {
        $userAddress = \app\api\model\UserAddress::where('user_id', '=', $this->uid)->find();
        if (!$userAddress)
        {
            throw new \app\lib\exception\UserException([
                'msg' => '收货地址不存在， 下单失败',
                'errorCode' => 60001
            ]);
        }

        return $userAddress->toArray();
    }

    // public function getOrderStatus()
    // {
    //     $status = [
    //         'pass' => true,
    //         'orderPrice' => 0,
    //         'pStatusArray' => [],
    //         'totalCount' => 0,

    //     ];

    //     foreach ($this->input as $oProudct)
    //     {
    //         $pStatus = $this->getProductStatus($oProudct['product_id'], $oProudct['count'], $this->products);
    //         if (!$pStatus['haveStock'])
    //         {
    //             $status['pass'] = false;
    //         }

    //         $status['orderPrice'] += $pStatus['totalPrice'];
    //         $status['totalCount'] += $pStatus['count'];
    //         array_push($status['pStatusArray'], $pStatus);
    //     }

    //     return $status;
    // }

    public function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex = -1;
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'name' => '',
            'totalPrice' => 0
        ];

        for ($i = 0; $i < count($products); $i++)
        {
            if ($oPID == $products[$i]['id'])
            {
                $pIndex = $i;
            }
        }

        if ($pIndex == -1)
        {
            throw new \app\lib\exception\OrderException([
                'msg' => 'id:'.$oPID.'商品不存在， 创建订单失败'
            ]);
        }
        else
        {
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['count'] = $oCount;
            $pStatus['name'] = $product['name'];
            $pStatus['totalPrice'] = $product['price'] * $oCount;
            if ($product['stock'] - $oCount >= 0)
            {
                $pStatus['haveStock'] = true;
            } 

            return $pStatus;
        }
    }
    // public function getProductsByOrder($input)
    // {
    //     $oPIDs = [];
    //     foreach ($input as $item) {
    //         array_push($oPIDs, $item['product_id']);
    //     }

    //     $products = \app\api\model\Product::all($oPIDs)->visible(['id', 'price', 'stock', 'name', 'main_img_url'])->toArray();

    //     return $products;
    // }
}