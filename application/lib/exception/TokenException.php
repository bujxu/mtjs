<?php
namespace app\lib\exception;

class TokenException extends BaseException
{
    public $code = 401;
    public $msg = 'token 已经过期或者是无效token';
    public $errorCode = 10001;
}