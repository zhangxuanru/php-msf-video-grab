<?php
/**
 * 欢迎
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace App\Controllers;

class Index extends Base
{
    public function actionIndex()
    { 
        $this->display();
    }

   public function actionMain()
   {
      $this->display();
   }

}

