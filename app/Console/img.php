<?php
/**
 * 图片服务
 */
define("WWW_DIR", realpath(dirname(__FILE__) . '/../../..'));
define('PID_DIR', WWW_DIR . '/runtime/img/pids/' );
class ImgServer{
    public $http;
    public static  $daemonize = 0;
    public static  $token = 'zxrlovedingjiao';
    public static  $_masterPid;
    public static  $pidFile = PID_DIR.'img.php.pid';
    public $logoPath  = '/data/storage/images/logo/';
    public $domainRoot= '/data/storage';
    public $domainArr = array(
           'logo' => 'http://upload.13520v.com/'
        );


    public function start()
    {
        $this->http = new swoole_http_server("127.0.0.1", 9503);
        $this->http->on("start",   [$this, 'onStart'] );
        $this->http->on("request", [$this, 'onRequest']);
        $this->http->on("shutdown",[$this,'onShutdown']);
        $set = $this->setServerSet();
        $set['daemonize'] = self::$daemonize ? 1 : 0;        
        $this->http->set($set);
        $this->http->start();
    }
    public  function onStart()
    {
        self::$_masterPid =  $this->http->master_pid;
        file_put_contents(self::$pidFile,  self::$_masterPid);
        file_put_contents(self::$pidFile, ',' .$this->http->manager_pid, FILE_APPEND);
        echo "Swoole http server is started at http://127.0.0.1:9503\n";
    }


    public function onShutdown()
    {
         echo 'swoole http server is stop ';
         $pidInfo = static::getServerPidInfo(); 
         $masterPid = $pidInfo['masterPid'];
         $managerPid = $pidInfo['managerPid'];  
         self::stopWorker($masterPid, 'img.php');
         exit(0);
    }


    public function onRequest($request, $response)
    {
        $ret = ['code' => '500'];
        $post  = $request->post;
        $files = $request->files;
        if(empty($post) || empty($files)){
            $ret['msg'] = '数据为空,请重新设置';
            $this->outputJson($response,$ret);
        }
        $type =  trim($post['type']);
        $token = trim($post['token']);
        if($token != self::$token){
            $ret['msg'] = 'token错误';
            $this->outputJson($response,$ret);
        }
        $fileName = (isset($post['fileName']) && !empty($post['fileName'])) ? $post['fileName'] : '';
        $picturePath =  $this->getPicturePath($type,$fileName);
        if(empty($picturePath) || empty($picturePath['path']) || empty($picturePath['savePath'])){
            $ret['msg'] = '路径错误';
            $this->outputJson($response,$ret);
        }
        $savePath = $picturePath['savePath'];
        $path     = $picturePath['path'];
        if(file_exists($savePath)){
             unlink($savePath);
        }
        $this->clearDir($path,$type);
        $tmpname  = $files['imgFile']['tmp_name'];
        if(move_uploaded_file($tmpname,$savePath)){
            $ret['msg']  = '上传成功';
            $ret['code'] = 200;
            $ret['url']  = $picturePath['url'];
        }else{
            $ret['msg'] = '上传失败';
        }
        $this->outputJson($response,$ret);
    }

    public function getPicturePath($type,$fileName='')
    {
      $path = '';
      $domain = isset($this->domainArr[$type]) ? $this->domainArr[$type] : '' ;
      switch($type){
          case 'logo':
              $fileName = empty($fileName) ? 'logo.jpg':$fileName;
              $path = $this->logoPath;             
              break;
      }
      if(!is_dir($path)){
         mkdir($path,0755,true);
      }
      $savePath = $path.$fileName;
      $urlPath  = $domain.str_replace($this->domainRoot,'',$savePath);
      $data = ['path' => $path,'savePath' => $savePath,'url' => $urlPath ];
      return $data;
    }

    /**
     * 清空目录
     * @param $dir
     */
    public  function clearDir($dir,$type)
    {
        switch($type){
            case 'logo':
                exec('rm -rf '.$dir.'/*');
                break;
        }
    }



    public function  outputJson($response,$data)
    {
        $response->end(json_encode($data));
    }

    /**
     * 配置选项
     */
    public function setServerSet()
    {

    }

   public static function writeln($messages)
    {
        $msgStr = (string)$messages;
        echo sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $msgStr);
    }

    /**
     * 停止当前worker.
     *
     * @param        $masterPid
     * @param string $startFile
     */
    protected static function stopWorker($masterPid, $startFile = '')
    {
        @unlink(self::$pidFile);
        self::writeln("$startFile is stoping ...");
        // Send stop signal to master process.
        $masterPid && posix_kill($masterPid, SIGTERM);
        // Timeout.
        $timeout = 5;
        $startTime = time();
        // Check master process is still alive?
        while (1) {
            $masterIsAlive = $masterPid && posix_kill($masterPid, SIG_BLOCK);
            if ($masterIsAlive) {
                // Timeout?
                if (time() - $startTime >= $timeout) {
                    writeln("{$startFile} stop fail");
                    exit;
                }
                // Waiting amoment.
                usleep(10000);
                continue;
            }
            // Stop success.
            self::writeln("{$startFile} stop success");
            break;
        }
    }

    /**
     * 获取当前服务器的pid数据.包含俩个key:
     * [
     *      'masterPid' => 主进程pid.
     *      'managerPid' => manager进程pid.
     * ]
     *
     * @return array|bool 如果master活着则返回pid信息,否则返回false.
     */
    protected static function getServerPidInfo()
    {
        $masterPid = $managerPid = null;
        if (file_exists(self::$pidFile)) {
            $pids = explode(',', file_get_contents(self::$pidFile));
            // Get master process PID.
            $masterPid = $pids[0];
            $managerPid = $pids[1];
            $masterIsAlive = $masterPid && @posix_kill($masterPid, SIG_BLOCK);
        } else {
            $masterIsAlive = false;
        }
        return $masterIsAlive ? [
            'masterPid' => $masterPid,
            'managerPid' => $managerPid,
        ] : false;
    }
    /**
     * 解析命令行参数
     *
     * @return void
     */
    public  function parseCommand()
    {
        global $argv;
        // Check argv;
        $startFile = $argv[0];
        if (!isset($argv[1])) {
            $argv[1] = 'start';
        }
        // Get command.
        $command = trim($argv[1]);
        $command2 = isset($argv[2]) ? $argv[2] : '';

        $pidInfo = static::getServerPidInfo();

        // Master is still alive?
        if ($pidInfo !== false) {
            if ($command === 'start' || $command === 'test') {
                self::writeln("{$startFile} already running");
                exit;
            }
        } elseif ($command !== 'start' && $command !== 'test') {
            self::writeln("{$startFile} not run");
            exit;
        }

        $masterPid = $pidInfo['masterPid'];
        $managerPid = $pidInfo['managerPid'];

        // execute command.
        switch ($command) {
            case 'start':
                if ($command2 === '-d') {
                    self::$daemonize = true;
                }
                break;
            case 'stop':
                self::stopWorker($masterPid, $startFile);
                exit(0);
                break;
            case 'restart':
                self::stopWorker($masterPid, $startFile);
                self::$daemonize = true;
                break;
            default:
        }
    }
}

$http = new ImgServer();
$http->parseCommand();
$http->start();


