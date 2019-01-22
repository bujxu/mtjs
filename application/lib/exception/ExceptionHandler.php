<?php

namespace app\lib\exception;

use Exception;
use think\exception\Handle; 
use think\Request;
class ExceptionHandler extends Handle
{
    // public function render(Exception $e)
    // {
    //     return json('xxxxxxxxxxxxxxxxxx');
    // }
    private $code;
    private $msg;
    private $errorCode;

    public function render(Exception $e)
    {
        if ($e instanceof BaseException)
        {
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        }
        else
        {
            //Config::get('app_debug');
            if (config('app_debug'))
            {
                return parent::render($e);
            }
            $this->code = 500;
            $this->msg = '服务器内部错误';
            $this->errorCode = 999;
        }

        $request = Request::instance();
        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => $request->url()
        ];

        return json($result, $this->code);
    }
}