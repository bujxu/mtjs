<?php

namespace app\api\controller\v1;

use think\Controller;
use app\api\model\Theme as ThemeModel;
use \app\lib\exception\ThemeException;
class Theme
{
    public function getSimpleList($ids='')
    {
        (new \app\api\validate\IDCollection)->goCheck();
        $ids = \explode(',', $ids);
        $result = ThemeModel::with('topicImg,headImg')->select($ids);
        if ($result->isEmpty())
        {
            throw new ThemeException;
        }
        return $result;
    }

    public function getComplexOne($id)
    {
        (new \app\api\validate\IDMustBePostiveInt)->goCheck();
        $theme = ThemeModel::getThemeWithProducts($id);
        if ($there->isEmpty())
        {
            throw new ThemeException;
        }
        return $theme;
    }
} 