<?php
namespace app\api\controller;
use app\api\service\Token as TokenService;

class BaseController extends \think\Controller
{
    public function checkPrimaryScope()
    {
        TokenService::needPrimaryScope();
    }

    
    public function checkExclusiveScope()
    {
        TokenService::needExclusiveScope();
    }

    public function checkDecodeData($encryptedData='', $iv='')
    {
        (new \app\api\validate\DecodeData)->goCheck();
    }

}