<?php
/**
 * grab_tags   视频标签
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\GrabTagsModel;

class GrabTagsLogic extends BaseLogic
{
    private $tagObj = null;

    public function __construct()
    {
        parent::__construct();
        $this->tagObj = $this->getObject(GrabTagsModel::class);
    }

    /**
     * 根据 ID数组 批量查询标签数据
     * @param array $idArr
     * @param string $field
     */
    public function getTagsByIdList($idArr=array())
    {
        $where = [
             'id' => ['symbol' => 'in','value' => $idArr ]
           ];
        try {
            $field = 'id,tag';
            $tagData = yield $this->tagObj->getList($field,$where);
        }catch(\Exception $e){
            return [];
        }
        return $tagData;
    }

    /**
     * 批量插入tag
     * @param $tags
     */
    public function batchInsert($tags)
    {
        $return = [];
        foreach($tags as $tag){
            if(is_array($tag)){
                $crcid = crc32($tag['tag_name']);
                $addData = array(
                    'tag'    => $tag['tag_name'],
                    'tag_id' => $tag['tag_id'],
                    'crc_id' => $crcid
                );
            }else{
                $crcid = crc32($tag);
                $addData = array(
                    'tag' => $tag,
                    'crc_id' => $crcid
                );
            }
            $where = [
                'crc_id' => ['symbol' => '=','value' => $crcid],
            ];
            $data =  yield $this->tagObj->getList('id',$where);
            if(!empty($data)){
                $return[] = $data[0]['id'];
                continue;
            }
            $ret  =  yield $this->tagObj->save($addData);
            $return[] = $ret;
        }
        return $return;
    }



    public function  destroy()
    {
        $this->tagObj = null;
        parent::destroy();
    }


}
