<?php
/**
 * 检查七牛生成视频HLS_KEY，如果数据库没有则补录入数据库
 *./console.php hls/checkhls
 */
namespace App\Console;

use PG\MSF\Client\Http\Client;

class Hls extends Base
{
    /**
     * 检查HLS_key是否生成成功
     * @return bool
     */
    public function actionCheckhls()
    {
        echo "--checkhls--start--\r\n";
        try{
           $search['hls_key'] =  ['symbol' => '=','value' => ''];
           $data =  yield  $this->getVideoExtendInstance()->getVideoExtendListData($search,[],'video_id,hls_key,hls_id');
           if(empty($data)){
               return true;
           }
            $successNumber = $errorNumber = 0;
            foreach($data as $index => $item){
                $hls_id = $item['hls_id'];
                $qiniuUrl = sprintf('https://api.qiniu.com/status/get/prefop?id=%s',$hls_id);
                $result = yield $this->getObject(Client::class)->goSingleGet($qiniuUrl);
                $hlsInfo = !empty($result['body']) ? json_decode($result['body'],true) : [];
                if(empty($hlsInfo)  || !isset($hlsInfo['code']) || $hlsInfo['code'] != '0'){
                     continue;
                }
                $hls_key = $hlsInfo['items'][0]['key'];
                $upData['hls_key'] = $hls_key;
                $ret =  yield  $this->getVideoExtendInstance()->updateExtendByVideoId($item['video_id'],$upData);
                if($ret){
                    $successNumber++;
                }else{
                    $errorNumber++;
                }
            }
        }catch(\Exception $e){
            echo $e->getMessage();
            return false;
        }
        echo "Checkhls--已执行成功,成功:{$successNumber} 失败:{$errorNumber}";
   }

}
