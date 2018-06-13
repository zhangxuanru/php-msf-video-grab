<?php
/**
 * youtube 播放列表
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/12/22
 * Time: 14:21
 */
namespace App\Models\Logic;
use App\Models\Model\VideoPlayListModel;

class VideoPlayListLogic extends BaseLogic
{
    
    public function __construct()
    {
        parent::__construct();
        $this->objPool = $this->getObject(VideoPlayListModel::class);
    }

    /**
     * 根据playlistId检查表中是否存在
     * @param int $playlistId
     */
    public function checkIdExists($playlistId = 0)
    {
        $where = [
            'playlistId' => ['symbol' => '=','value' => $playlistId]
        ];
        $data =  yield $this->objPool->getList('id',$where);
        return $data;
    }
 
    public function  destroy()
    {
        $this->objPool = null;
        parent::destroy();
    } 
}
