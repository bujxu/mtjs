<?php 

namespace app\api\service;
use app\api\model\User as UserModel;
class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code)
    {   
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'), $this->wxAppID, $this->wxAppSecret, $this->code);
    }
    
    public function get()
    {
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult))
        {
            throw new \think\Exception('微信内部异常');
        }
        else
        {
            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail)
            {
                $this->processLoginError($wxResult);
            }
            else
            {
                return $this->grantToken($wxResult);
            }
        }
    }
    
    private function grantToken($wxResult)
    {
        $openid = $wxResult['openid'];

        $user = UserModel::getByOpenID($openid);
        if ($user)
        {
            $uid = $user->id;
        }
        else
        {
            $uid = $this->newUser($openid);
            
        }

        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);

        return $token;
    }

    private function saveToCache($cachedValue)
    {
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');
        $request = cache($key, $value, $expire_in);
        if (!$request)
        {
            throw new \app\lib\exception\TokenException([
                    'msg' => '服务器缓存异常',
                    'errCode' => '10005',
                ]);
        }

        return $key;
    }

    private function prepareCachedValue($wxResult, $uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        $cachedValue['scope'] = \app\lib\enum\ScopeEnum::User;
        return $cachedValue;
    }

    private function newUser($openid)
    {
        $user = UserModel::create(['openid' => $openid]);
        return $user->id;
    }

    private function processLoginError($wxResult)
    {
        throw new \app\lib\exception\WeChatException(
            [
                'msg' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]);
    }
}