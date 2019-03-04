<?php

namespace app\api\controller\v1;
use app\api\service\Token as TokenService;
use app\api\model\UserAddress;
use app\api\model\User as UserModel;

class Address extends \app\api\controller\BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress']
    ];


    public function createAddress()
    {
        $data = input('post.');
        
        $uid = TokenService::getCurrentUid();
        $address = new UserAddress;
        $address->data([
            "user_id" => $uid,
            'contact' => $data['contact'],
            'house_number' => $data['houseNumber'],
            'address_detail' => $data['addressDetail'],
            'phone_number' => $data['phoneNumber'],
            'status' => $data['status'],
        ])->save();

        if ($data['status'] == 'DEFAULT')
        {
            self::defaultAddress($address->id);
        }
    }

    public function deleteAddress()
    {
        $id = input('get.address_id');
        UserAddress::destroy([$id]);
    }

    public function defaultAddress($id=0)
    {
        if ($id == 0)
        {
            $id = input('post.address_id');
        }

        $uid = TokenService::getCurrentUid();
        $addressList = UserAddress::where(['user_id' => $uid])->select();
        for ($index = 0; $index < count($addressList); $index++)
        {
            if ($addressList[$index]->id == $id)
            {
                $addressList[$index]->status = 'DEFAULT';
            }
            else
            {
                $addressList[$index]->status = 'COMMON';
            }
            $addressList[$index]->save();
        }
    }

    public function modifyAddress()
    {
        $data= input('post.');
        $address = UserAddress::where(['id' => $data['address_id']])->find();
        $data = [
            'contact' => $data['contact'],
            'house_number' => $data['houseNumber'],
            'address_detail' => $data['addressDetail'],
            'phone_number' => $data['phoneNumber'],
            'status' => $data['status'],
        ];

        $address->save($data);
        if ($data['status'] == 'DEFAULT')
        {
            self::defaultAddress($data['address_id']);
        }
    }
    public function commitAddress()
    {
        $data = input('post.');
        
        $uid = TokenService::getCurrentUid();
        $address = new UserAddress;
        $address->data([
            "user_id" => $uid,
            'name' => $data['contact'],
            'house_number' => $data['houseNumber'],
            'detail_address' => $data['addressDetail'],
            'mobile' => $data['phoneNumber'],
            'status' => $data['status'],
        ]);
        $address->save();

    }

    public function getAddress()
    {
        $uid = TokenService::getCurrentUid();
        $result = UserAddress::all(['user_id' => $uid])->toArray();
        
        return $result;
    }

    public function getDefaultAddress()
    {
        $uid = TokenService::getCurrentUid();
        $result = UserAddress::get(['user_id' => $uid, 'status' => 'DEFAULT']);
        
        return $result;
    }
    
    public function createOrUpdateAddress()
    {
        $validate = new \app\api\validate\AddressNew;
        $validate->goCheck();
        $uid = TokenService::getCurrentUid();
        $user = UserModel::get($uid);
        if (!$user)
        {
            throw new \app\lib\exception\UserException;
        }

        $dataArray = $validate->getDataByRule(input('post.'));
        $userAddress = $user->address;

        if (!$userAddress)
        {
            $user->address()->save($dataArray);
        }
        else
        {
            $user->address->save($dataArray);
        }


        return json(new \app\lib\exception\SuccessMessage(), 201);
    }
}