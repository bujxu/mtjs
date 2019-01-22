<?php

namespace app\api\service;
use app\api\model\Image as ImageModel;
class Upload
{
    public static function uploadPicture()
    {
        $upload_config = Config('setting.upload_config');
        \think\Loader::import('ORG.Net.UploadFile', '', '.class.php');
        $dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
        
        $upload_config['savePath'] .= $dir_name.'/';
        $upload = new \UploadFile($upload_config);// 实例化上传类
        if(!$upload->upload()) {// 上传错误提示错误信息
            return array('error'=>1,'message'=>$upload->getErrorMsg());
        } else {// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();

            $image = new ImageModel;
            $image->from = 1;
            // $image->url = Config('setting.web_url'). 'Uploads/image/' . $info[0]['savename'];
            $image->url = $info[0]['savename'];

            $image->save();
            return array('error' => 0,'image_id'=>$image->id); 
        }
    }
}