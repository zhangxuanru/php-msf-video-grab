<?php
/**
 * grab_video_tags   视频标签逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\VideoTagsModel;

class VideoTagsLogic extends BaseLogic
{
    private $tagObj = null;

    public function __construct()
    {
        parent::__construct();
        $this->tagObj = $this->getObject(VideoTagsModel::class);
    }

    /**
     * 根据video_id获取视频标签表里数据
     * @param $id
     * @param string $field
     * @return array
     */
    public function getVideoTagByVideoId($video_id,$field='')
    {
        if(empty($field)){
            $field = 'tag_id';
        }
        $where = [
            'video_id' => ['symbol' => '=','value' => $video_id]
        ];
        try {
            $data = yield $this->tagObj->getList($field, $where);
        }catch(\Exception $e){
            return [];
        }
        return $data;
    }



    /**
     * 批量插入tag
     * @param int  $video_id
     * @param  array $tags_id
     */
    public function batchInsert($video_id,$tags_id)
    {
        $return = [];
        foreach($tags_id as $tagid){
            $where = [
                'video_id' => ['symbol' => '=', 'value' => $video_id ],
                'tag_id'   => ['symbol' => '=', 'value' => $tagid    ],
            ];
            $data =  yield $this->tagObj->getList('id',$where);
            if(!empty($data)){
                $return[] = $data[0]['id'];
                continue;
            }
            $addData = array(
                'video_id' => $video_id,
                'tag_id'   => $tagid
            );
            $result   =  yield $this->tagObj->save($addData);
            $return[] = $result;
        }
        return $return;
    }


    

    public function  destroy()
    {
        $this->tagObj = null;
        parent::destroy();
    }


}
