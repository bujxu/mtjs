<?php
namespace app\api\validate;

class IDCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIDs'
    ];

    protected $message = [
        'ids' => 'ids 必须是正数'
    ];

    protected function checkIDs($value)
    {
        $value = explode(',', $value);
        if (empty($value))
        {
            return false;
        }

        foreach ($value as $id)
        {
            if (!$this->isPositiveInteger($id))
            {
                return false;
            }
        }

        return true;
    }
}