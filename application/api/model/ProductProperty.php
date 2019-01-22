<?php

namespace app\api\model;

class ProductProperty extends BaseModel
{
    protected $hidden = ['product_id', 'delete_time', 'id'];

    public function imgUrl()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}