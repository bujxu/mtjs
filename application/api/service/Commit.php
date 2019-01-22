<?php

namespace app\api\service;
use app\api\model\CommitImage as CommitImageModel;
use app\api\model\Commit as CommitModel;
use app\api\model\Image as ImageModel;
class Commit
{
    public static function createCommit($groupId, $userId, $content)
    {   
        $commit = new CommitModel();
        $commit->group_id = $groupId;
        $commit->user_id = $userId;
        $commit->content = $content;

        $commit->save();

        return $commit->id;
    }

    public static function createCommitImage($commitId, $images)
    {   
        $imagesId = array_values($images);
        $length = count($imagesId);
        for ($index = 0; $index < $length; $index++)
        {
            $commitImage = new CommitImageModel();
        
            $commitImage->image_id = $imagesId[$index];
            $commitImage->commit_id = $commitId;

            $commitImage->save();
        }

        return ;
    }

    public static function deleteCommitImage($images)
    {
        $imagesId = array_values($images);
        $length = count($imagesId);
        for ($index = 0; $index < $length; $index++)
        {
            ImageModel::destroy($imagesId[$index]);
            CommitImageModel::destroyCommitImageByImageId($imagesId[$index]);
        }
    }


    public static function modifyCommitImage($content, $commitId, $images)
    {   
        $commitOrignal = self::getCommit($commitId);
        
        $addImages = array_diff($images, $commitOrignal['imagesId']);
        self::createCommitImage($commitId, $addImages);

        $delImages = array_diff($commitOrignal['imagesId'], $images);
        self::deleteCommitImage($delImages);

        CommitModel::modifyContent($commitId, $content);

        return ;
    }
     
    public static function deleteCommit($commitId)
    {
        CommitModel::destroy([$commitId]);
    }

    public static function editCommit($commitId)
    {
        
    }


    public static function getCommit($commitId)
    {
        $commit = CommitModel::getCommit($commitId);
        $images = array_column(array_column($commit['commit_images'], 'image'), 'url');
        $imagesId = array_column(array_column($commit['commit_images'], 'image'), 'id');
        $content = $commit['content'];

        return array('content' => $content, 'images' => $images, 'imagesId' => $imagesId);
    }

    public static function getGroupCommit($groupId)
    {
        $time = time();
        $commits = CommitModel::getGroupCommit($groupId);
        for ($index = 0; $index < count($commits); $index++)
        {
            $result[$index]['content'] = $commits[$index]['content'];
            $result[$index]['images'] = $commits[$index]['commit_images'][$time % (count($commits[$index]['commit_images']))]['image']['url'];
            $result[$index]['commitId'] = $commits[$index]['id'];
            $result[$index]['time'] = $commits[$index]['create_time'];
            $result[$index]['count'] = count($commits[$index]['commit_images']);
        }

        return $result;
    }
}