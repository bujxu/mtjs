<?php
namespace app\api\controller\v1;

use think\Controller;
use app\api\model\Product as ProductModel;
class Product 
{
    public function getRecent($count=15)
    {
        (new \app\api\validate\Count)->goCheck();
        $product =  ProductModel::getMostRecent($count);
        if ($product->isEmpty())
        {
            throw new \app\lib\exception\ProductException;
        }
        $product = $product->hidden(['summary']);
        return $product;
    }

    public function getAllInCategory($id)
    {
        (new \app\api\validate\IDMustBePostiveInt)->goCheck();
        $products = ProductModel::getProductsByCategoryID($id);
        if ($products->isEmpty())
        {
            throw new \app\lib\exception\ProductException;
        }

        $products = $products->hidden(['summary']);
        return $products;
    }

    public function getOne($id)
    {
        (new \app\api\validate\IDMustBePostiveInt)->goCheck();
        $product = ProductModel::getProductDetail($id);
        if (!$product)
        {
            throw new \app\lib\exception\ProductException;
        }

        return $product;
    }
    
}   