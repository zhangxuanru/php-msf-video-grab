<?php
/**
 * 输出字幕等配置信息
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace App\Controllers;
use PG\MSF\Controllers\Controller;

class Info extends Controller
{
    //视频临时保存的路径
    private $videoTmpDir = "/data/video/download/";

    /**
     * 输出视频字幕
     */
    public function actionSubtitle()
    {
        exec(' ls ' . $this->videoTmpDir . '*.en.srt', $arr);
        if(!empty($arr)){
            $audioFile = $arr[0];
            $content = file_get_contents($audioFile);
            $this->output($content);
            return true;
        }
        exec(' ls ' . $this->videoTmpDir . '*.srt', $arr);
        if(empty($arr)){
            $this->output('');
            return true;
        }
        $audioFile = $arr[0];
        $content = file_get_contents($audioFile);
        $this->output($content);
        return true;
    }

    /**
     * 视频播放时的验签
     */
    public function actionHk()
    {
        header("Access-Control-Allow-Origin: *");
        $key = 'loveDingJiaolove';
        $this->output($key);
    }
}

