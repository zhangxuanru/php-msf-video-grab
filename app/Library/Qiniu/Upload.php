<?php
/**
 * 七牛上传文件类
 * Created by PhpStorm.
 * User: zxr
 * Date: 2017/11/16
 * Time: 16:14
 */
namespace App\Library\Qiniu;
use Qiniu\Processing\PersistentFop;

// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;

class Upload {
    // 需要填写你的 Access Key 和 Secret Key
    private $accessKey = 'yv1gS6cG4eep4dH3gbj1wz9VJXuKgAYGSNNqnfS7';
    private $secretKey = 'qMUtVMGbs7qVTHR84tD4cOYum-nd_Z_1-MjTtEDs';
    private $videoBucket  = 'grab-videos';
    private $imageBucket  = "grab-images";
    private $pipeline     = 'grabVideo';

    //hls 配置
    private $keyStr       = "loveDingJiaolove";
    //hls播放验签URL
    private $keyUrl       = 'http://grab.13520v.com/info/hk';
    //字幕URL
    private $subtitleUrl  = 'http://manager.grabs.13520v.com/info/subtitle';
    //视频临时保存的路径
    private $videoTmpDir = "/data/video/download/";

    /**
     * 上传文件到七牛
     * @param $filePath
     * @param $fileName
     * @param string $type
     * @throws \Exception
     */
    public function uploadFile($filePath,$fileName,$type='' )
    {
      // 构建鉴权对象
        $auth = new Auth($this->accessKey, $this->secretKey);
        $bucket = $this->videoBucket;
        if($type == 'images'){
            $bucket = $this->imageBucket;
        }
       // 生成上传 Token
        $token = $auth->uploadToken($bucket);
       // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
       // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $fileName, $filePath);
        $data = ['hash' => '','key' => ''];
        if ($err !== null){
             return $data;
        }else {
             return $ret;
        }
    }

    /**
     * 上传文件并转成MP4格式
     * @param $filePath
     * @param $fileName
     * @param string $type
     * @throws \Exception
     */
    public function uploadFileByMp4($filePath,$fileName,$type='')
    {
        // 构建鉴权对象
        $auth = new Auth($this->accessKey, $this->secretKey);
        $bucket = $this->videoBucket;
        if($type == 'images'){
            $bucket = $this->imageBucket;
        }
        //转码时使用的队列名称
        $pipeline = $this->pipeline;
        //要进行转码的转码操作
        $fops = "avthumb/mp4";
        $policy = array(
            'persistentOps' => $fops,
            'persistentPipeline' => $pipeline
        );
        $uptoken = $auth->uploadToken($bucket, null, 7200, $policy);
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($uptoken, $fileName, $filePath);
        echo "\n====> putFile result: \n";
        if ($err !== null) {
            var_dump($err);
        } else {
            var_dump($ret);
        }
    }


    /**
     * @param $fileName
     * @return bool
     * @throws \Exception
     *  z1.5a2e59d58a3c0c379465ed26Array
    (
    [code] => 0
    [desc] => The fop was completed successfully
    [id] => z1.5a2e59d58a3c0c379465ed26
    [inputBucket] => grab-videos
    [inputKey] => bili1512987071594.mp4
    [items] => Array
    (
    [0] => Array
    (
    [cmd] => avthumb/m3u8/noDomain/1/vb/5m/segtime/10/hlsKey/YWJjZGVmZzEyMzQ1NjV5Nw==/hlsKeyUrl/aHR0cDovL2dyYWIuMTM1MjB2LmNvbS90ZXN0/pipeline/grabVideo
    [code] => 0
    [desc] => The fop was completed successfully
    [hash] => FoiUFwzJishcbMuSJoSrzz8XjsJd
    [key] => GAiY3FzEgg-GntIkyhxLtBIs8FA=/luAfRfEvnkmwxnovCt5fS648RU8G
    [returnOld] => 1
    )

    )

    [pipeline] => 1381169055.grabVideo
    [reqid] => rS8AAA8vyLu-Nf8U
    )
    z1.5a2e59fb8a3c0c379465f6fcArray
    (
    [code] => 2
    [desc] => The fop is executing now
    [id] => z1.5a2e59fb8a3c0c379465f6fc
    [inputBucket] => grab-videos
    [inputKey] => bili1512987108902.mp4
    [items] => Array
    (
    [0] => Array
    (
    [cmd] => avthumb/m3u8/noDomain/1/vb/5m/segtime/10/hlsKey/YWJjZGVmZzEyMzQ1NjV5Nw==/hlsKeyUrl/aHR0cDovL2dyYWIuMTM1MjB2LmNvbS90ZXN0/pipeline/grabVideo
    [code] => 2
    [desc] => The fop is executing now
    [returnOld] => 0
    )

    )

    [pipeline] => 1381169055.grabVideo
    [reqid] => rS8AANKXvxnINf8U
    )
     */
    public function modifyFileByHls($fileName)
    {
      try{
        $data = [];
        $content = $fileName."--modifyFileByHls--start--\r\n";
        $this->writeLog($content);
        // 构建鉴权对象
        $auth = new Auth($this->accessKey, $this->secretKey);
        $bucket = $this->videoBucket;
        //转码时使用的队列名称
        $pipeline = $this->pipeline;

        //要进行转码的转码操作
        $hlsKey = \Qiniu\base64_urlSafeEncode($this->keyStr);
        $hlsKeyUrl = \Qiniu\base64_urlSafeEncode($this->keyUrl);

        //$fops = 'avthumb/m3u8/noDomain/1/s/720x480/vb/5m/hlsKey/'.$hlsKey.'/hlsKeyUrl/'.$hlsKeyUrl.'/notifyURL/'.$notifyURL.'/pipeline/'.$pipeline;
       //  $fops = 'avthumb/m3u8/noDomain/1/vb/5m/segtime/10/hlsKey/'.$hlsKey.'/hlsKeyUrl/'.$hlsKeyUrl.'/pipeline/'.$pipeline;

       $audioFile =  $this->checkAudioFileExists();
       if(!empty($audioFile)){
           $subtitleUrl =  \Qiniu\base64_urlSafeEncode($this->subtitleUrl);
           $fops = 'avthumb/m3u8/noDomain/1/segtime/60/subtitle/'.$subtitleUrl.'/hlsKey/'.$hlsKey.'/hlsKeyUrl/'.$hlsKeyUrl;
        }else{
           $fops = 'avthumb/m3u8/noDomain/1/segtime/60/hlsKey/'.$hlsKey.'/hlsKeyUrl/'.$hlsKeyUrl;
       }
        $content = $fileName."--start--\r\n";
        $this->writeLog($content);
       // $fops = 'avthumb/m3u8/noDomain/1/segtime/15/hlsKey/'.$hlsKey.'/hlsKeyUrl/'.$hlsKeyUrl;
        $pfop = new PersistentFop($auth, null);
        list($id, $error) = $pfop->execute($bucket, $fileName, $fops,$pipeline);
        $data['id'] = $id;
        if(!empty($error)){
            $content = $fileName."--error1--\r\n".var_export($error,true);
            $this->writeLog($content);
            $this->rmAudioFile($audioFile);
            return $data;
        }
        sleep(5);
        list($status, $error) = $pfop->status($id);
        $content = $fileName."--status--\r\n".var_export($status,true)."\r\n";
        $this->writeLog($content);
        if(!empty($error)){
            $content = $fileName."--error2--\r\n".var_export($error,true)."\r\n";
            $this->writeLog($content);
            $this->rmAudioFile($audioFile);
            return $data;
        }
        $index = 0;
        while($status['code'] != '0'){
            sleep(5);
            list($status, $error) = $pfop->status($id);
            if(!empty($error)){
                $content = $fileName."--error{$index}--\r\n".var_export($error,true)."\r\n";
                $this->writeLog($content);
                $this->rmAudioFile($audioFile);
                return $data;
            }
            if($status['code'] != '0'){
                $content = $fileName."--status--{$index}--\r\n".var_export($status,true)."\r\n";
                $this->writeLog($content);
            }
            if($status['code']  == '0'){
                $content = $fileName."--success--\r\n".var_export($status,true);
                $this->writeLog($content);
                $this->rmAudioFile($audioFile);
                $data['key'] = $status['items'][0]['key'];
                return  $data;
            }
            $index++;
            if($index > 10){
                break;
            }
        }
       sleep(5);
       list($status, $error) = $pfop->status($id);
       if($status['code'] != '0'){
           $content = $fileName."--error4--\r\n".var_export($status,true);
           $this->writeLog($content);
           $this->rmAudioFile($audioFile);
           return $data;
       }
       $content = $fileName."--success1--\r\n".var_export($status,true);
       $this->writeLog($content);
       $this->rmAudioFile($audioFile);
       $data['key'] = $status['items'][0]['key'];
       return  $data;
     }catch(\Exception $e){
            throw new \Exception('modifyFileByHls error');
        }
    }


    /**
     * 检查音频文件是否存在
     * @return string
     */
    public function checkAudioFileExists()
    {
        exec(' ls ' . $this->videoTmpDir . '*.en.srt', $arr);
        if(!empty($arr)){
            return $arr[0];
        }
        exec(' ls ' . $this->videoTmpDir . '*.srt', $arr);
        if (!empty($arr)) {
               return $arr[0];
        }
        return '';
    }

    /**
     * 记录七牛日志
     * @param string $content
     */
    public function writeLog($content='')
    {
        $fileName = $this->videoTmpDir.'/qiniu/qiniu_'.date('Ymd').'.log';
        file_put_contents($fileName,$content,FILE_APPEND);
    }

    /**
     * 删除音频文件
     * @param $audioFile
     */
    public function rmAudioFile($audioFile)
    {
        if(empty($audioFile) || !file_exists($audioFile)){
              return true;
        }
        unlink($audioFile);
        usleep(3);
        if(file_exists($audioFile)){
            exec("rm -rf ".$audioFile);
        }
        exec('rm -rf '.$this->videoTmpDir."*.srt ");
    }

}



