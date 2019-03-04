<?php

namespace app\api\controller\v1;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use \app\api\service\Upload as UploadService;
class Order extends \app\api\controller\BaseController
{

    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder']
    ];

    public function orderCreate()
    {
        // (new \app\api\validate\OrderPlace)->goCheck();
        $input = input('post.');
        $uid = TokenService::getCurrentUid();
        
        $order = new OrderService();
        $status = $order->place($uid, $input);
        
        return $status;
    }
    
    public function orderDelete()
    {
        $orderId = input('post.orderId');
        return OrderService::orderDelete($orderId);
    }

    public function orderStatus()
    {
        $input = input('post.');
        $uid = TokenService::getCurrentUid();

        return OrderService::orderStatus($input, $uid);
    }

    public function placeOrder()
    {
        // (new \app\api\validate\OrderPlace)->goCheck();
        $input = input('post.');
        $uid = TokenService::getCurrentUid();
        
        $order = new OrderService();
        $status = $order->place($uid, $input);
        
        return $status;
    }
    
    public function getOrderDetail()
    {
        $id = input('get.order_id');
        $result = OrderService::getOrderDetail($id);
        return $result;
    }
    
    public function getOrder()
    {
        $uid = TokenService::getCurrentUid();
        if (input('get.isSeller') == 0)
            $result = OrderService::getOrder($uid);
        else
            $result = OrderService::getOrderBySellerId($uid);
        return $result;
    }

    public function uploadImage()
    {
        $result = UploadService::uploadPicture();

        return $result;
    }

    public function userUploadAdd()
    {
        $content = input('post.');

        $userId = TokenService::getCurrentUid();
        $GoodId = OrderService::createOrderImage($userId, $content);

        return array('error' => 0);
    }

    public function userUploadModify()
    {
        // $images = json_decode(input('post.images'), true);
        $content = input('post.');

        OrderService::userUploadModify($content);

        return array('error' => 0);
    }
}