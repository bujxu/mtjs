<?php 

namespace app\api\validate;

class GroupUsersGet extends BaseValidate
{
    protected $rule = [
        'groupId' => 'require|isNotEmpty',
    ];
}