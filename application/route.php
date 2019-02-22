<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------



use think\Route;

// Route::rule('xubuju', 'index/Index/getBanner');

//Route::rule('banner/:id', 'api/Banner/getBanner');

// Route::get('api/v1/banner/:id', 'api/v1.Banner/getBanner');
Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');


Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');
Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne');   


Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');
Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');
Route::get('api/:version/product/:id', 'api/:version.Product/getOne', [], ['id' => '\d+']);

// Route::group('api/:version/product', 
// function ()
// {
//     Route::get('/recent', 'api/:version.Product/getRecent');
//     Route::get('/:id', 'api/:version.Product/getOne', [], ['id' => '\d+']); 
//     Route::get('/by_category', 'api/:version.Product/getAllInCategory');
// }
// );


Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');

Route::post('api/:version/token/user', 'api/:version.Token/getToken');
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');
Route::post('api/:version/token/app', 'api/:version.Token/getAppToken');
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');

Route::post('api/:version/order', 'api/:version.Order/placeOrder');
Route::get('api/:version/order/getOrderDetail', 'api/:version.Order/getOrderDetail');
Route::get('api/:version/order/getOrder', 'api/:version.Order/getOrder');

Route::post('api/:version/decode/share', 'api/:version.Decode/decodeShare');
Route::post('api/:version/decode/user', 'api/:version.Decode/decodeUser');

Route::get('api/:version/decode/getGroups', 'api/:version.Decode/getGroups');
Route::get('api/:version/group/getGroupUsers', 'api/:version.Group/getGroupUsers');
Route::get('api/:version/group/getGroupCommit', 'api/:version.Group/getGroupCommit');
// Route::get('api/:version/user/getGroupUserInfo', 'api/:version.User/getGroupUserInfo');
Route::post('api/:version/group/userUploadAdd', 'api/:version.Group/userUploadAdd');
Route::post('api/:version/group/userUploadModify', 'api/:version.Group/userUploadModify');
Route::get('api/:version/group/userUploadDel', 'api/:version.Group/userUploadDel');
Route::get('api/:version/group/groupUserCommit', 'api/:version.Group/groupUserCommit');
Route::get('api/:version/group/getCommit', 'api/:version.Group/getCommit');
// Route::get('api/:version/Address/second', 'api/:version.Address/second');
Route::post('api/:version/address/commitAddress', 'api/:version.Address/commitAddress');
Route::get('api/:version/address/getAddress', 'api/:version.Address/getAddress');
Route::post('api/:version/user/login', 'api/:version.User/login');
//good
Route::post('api/:version/good/uploadImage', 'api/:version.Good/uploadImage');
Route::post('api/:version/good/userUploadAdd', 'api/:version.Good/userUploadAdd');
Route::post('api/:version/good/userUploadModify', 'api/:version.Good/userUploadModify');
Route::get('api/:version/good/goodList', 'api/:version.Good/goodList');
Route::get('api/:version/good/goodDelete', 'api/:version.Good/goodDelete');
Route::get('api/:version/good/getGoodInfo', 'api/:version.Good/getGoodInfo');
//shop
Route::get('api/:version/shop/getShopInfo', 'api/:version.Shop/getShopInfo');
Route::post('api/:version/shop/shopCreate', 'api/:version.Shop/shopCreate');
Route::post('api/:version/shop/shopEdit', 'api/:version.Shop/shopEdit');
Route::get('api/:version/shop/getShopListInfo', 'api/:version.Shop/getShopListInfo');
Route::get('api/:version/shop/getShopInfoByShopId', 'api/:version.Shop/getShopInfoByShopId');
Route::post('api/:version/address/createAddress', 'api/:version.Address/createAddress');
Route::post('api/:version/address/modifyAddress', 'api/:version.Address/modifyAddress');
Route::post('api/:version/address/defaultAddress', 'api/:version.Address/defaultAddress');
Route::get('api/:version/address/deleteAddress', 'api/:version.Address/deleteAddress');
