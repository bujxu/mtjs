<?php

namespace app\api\controller\v1;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
class Order extends \app\api\controller\BaseController
{

    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder']
    ];


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
        $orderId = input('get.orderId');
        $result = OrderModel::get(['id' => $orderId])->toArray();
        return $result;
    }
    
    public function getOrder()
    {
        $status = input('get.status');
        $uid = TokenService::getCurrentUid();
        if ($status == 10)
            $result = OrderModel::where(['user_id' => $uid])->select()->toArray();
        else
            $result = OrderModel::where(['user_id' => $uid, 'status' => $status])->select()->toArray();
        
        return $result;
    }
}