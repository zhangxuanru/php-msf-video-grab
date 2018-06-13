<?php
/**
 * grab_video_images   视频图片逻辑层
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\VideoImagesModel;

class VideoImagesLogic extends BaseLogic
{
    
    private  $field = 'img_source_url,fillename,width,height,is_cover,qiniu_upload,is_del';

    public function __construct()
    {
        parent::__construct();
        $this->objPool = $this->getObject(VideoImagesModel::class);
    }

    /**
     * 根据video_id获取视频图片数据
     * @param $id
     * @param string $field
     * @return array
     */
    public function getVideoImagesByVideoId($video_id,$field='')
    {
        if(empty($field)){
            $field = $this->field;
        }
        $where = [
            'video_id'=>['symbol' => '=','value' => $video_id]
        ];
        try {
            $data = yield $this->objPool->getList($field, $where);
        }catch(\Exception $e){
             return [];
        }
        return $data;
    }
 
    public function  destroy()
    {
        $this->objPool = null;
        parent::destroy();
    }


}
