<?php

namespace app\api\validate;

class OrderPlace extends BaseValidate
{
    protected $rule = [
        'products' => 'checkProducts'
    ];

    protected $singRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];

    protected function checkProducts($values)
    {
        if (!\is_array($values))
        {
            throw new \app\lib\exception\ParameterException([
                'msg' => '商品参数不正确'
            ]);
        }

        if (empty($values))
        {
            throw new \app\lib\exception\ParameterException([
                'msg' => '商品列表不能为空'
            ]);
        }

        foreach ($values as $value)
        {
            $this->checkProduct($value);
        }

        return true;
    }

    protected function checkProduct($value)
    {
        $validate = new \app\api\validate\BaseValidate($this->singRule);
        $result = $validate->check($value);
        if (!$result)
        {
            throw new \app\lib\exception\ParameterException([
                'msg' => '商品列表参数错误'
            ]);
        }
    }
}