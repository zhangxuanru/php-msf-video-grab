<?php
/**
 * 执行具体的抓取
 *./console.php grabexec/run -id="90"
 */
namespace App\Console;

class Grabexec extends Base
{
    /**
     * 单个视频下载
     * @return bool
     */
    public function actionRun()
    {
        try{
            $id  = $this->getContext()->getInput()->get('id');
            $data = yield $this->getBasicData($id);
            $downloadObj = $this->getBasicModel($data);
            //这里将grab_information表中status改为4,改成正在执行中
             yield $this->updateGranInfoStatus($id,self::EXEC_RUN_STATUS);
        }catch(\Exception $e){
            echo $e->getMessage();
            return false;
        }
        try{
            yield $downloadObj->runSingleVideo();
        }catch(\Exception $e){
            echo  $e->getMessage();
            return false;
        }
        echo "ID:--".$id."--已执行成功";
   }

   

    /**
     * 抓取单页视频
     */
  public function actionPage()
  {
      try{
          $id  = $this->getContext()->getInput()->get('id');
          $data = yield $this->getBasicData($id);
          $downloadObj = $this->getBasicModel($data);
          //这里将grab_information表中status改为4,改成正在执行中
          yield $this->updateGranInfoStatus($id,self::EXEC_RUN_STATUS);
      }catch(\Exception $e){
          echo $e->getMessage();
          return false;
      }
      try{ 
           yield $downloadObj->runPageVideoDownload(); 
      }catch(\Exception $e){
          echo  $e->getMessage();
          return false;
      }
      echo "ID:--".$id."--已执行成功,其中成功：".$downloadObj->success_number."--失败:".$downloadObj->fail_number;
  }

}
