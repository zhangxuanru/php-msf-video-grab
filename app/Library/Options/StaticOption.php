<?php
/**
 * 基类
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace App\Library\Options;
 
/**
 * Class Child
 * @package PG\MSF\Base
 */
class StaticOption
{
    public static function options($page='')
    { 
       $common = [
         'style'  => [],
         'script' => []          
       ];
        $info = [
            'title' => '13520企业网站管理系统',
            'style' => [

            ],
            'script' => [
                
            ]
        ];
      switch ($page) {
        case 'index.index':
           $info = [               
                   'title' => '13520企业网站管理系统',
                   'style' => [
                                       
                    ],
                  'script' => [
                      '/lib/jquery.contextmenu/jquery.contextmenu.r2.js',
                      '/lib/page/index.js',                       
                    ] 
               ];       
          break;

          case 'grab.index':
          case 'grab.page' :
          case 'grab.pagelist':
          case 'cate.index':
          case 'video.index':
          case 'video.catdata':
          case 'user.index':
                $info = [               
                   'title' => '13520企业网站管理系统',
                   'style' => [
                                       
                    ],
                  'script' => [
                      '/lib/My97DatePicker/4.8/WdatePicker.js',
                      '/lib/datatables/1.10.0/jquery.dataTables.min.js',   
                      '/lib/laypage/1.2/laypage.js',
                      '/lib/page/func.js'
                    ] 
               ];       
          break;
          case 'grab.add':
          case 'cate.add':
          case 'video.add':
          case 'nav.addnav':
          case 'user.add':
          case 'login.info':
              $info = [
                  'title' => '13520企业网站管理系统',
                  'style' => [

                  ],
                  'script' => [
                      '/lib/jquery.validation/1.14.0/jquery.validate.js',
                      '/lib/jquery.validation/1.14.0/validate-methods.js',
                      '/lib/jquery.validation/1.14.0/messages_zh.js'
                  ]
              ];
              break;

          case 'login.index':
              $info = [
                  'title' => '13520企业网站管理系统',
                  'style' => [
                        '/static/h-ui-admin/css/H-ui.login.css'
                  ],
                  'script' => [
                      '/lib/jquery.validation/1.14.0/jquery.validate.js',
                      '/lib/jquery.validation/1.14.0/validate-methods.js',
                      '/lib/jquery.validation/1.14.0/messages_zh.js'
                  ]
              ];
              break;

          case 'nav.index':
              $info = [
                  'title' => '13520企业网站管理系统',
                  'style' => [

                  ],
                  'script' => [
                      '/lib/html5shiv.js',
                      '/lib/respond.min.js',
                      '/lib/DD_belatedPNG_0.0.8a-min.js',
                      '/lib/My97DatePicker/4.8/WdatePicker.js',
                      '/lib/datatables/1.10.0/jquery.dataTables.min.js',
                      '/lib/laypage/1.2/laypage.js',
                      '/lib/page/func.js'
                  ]
              ];
            break;

          case 'video.edit':
              $info = [
                  'title' => '13520企业网站管理系统',
                  'style' => [
                        '/lib/webuploader/0.1.5/webuploader.css'
                  ],
                  'script' => [
                      '/lib/html5shiv.js',
                      '/lib/respond.min.js',
                      '/lib/DD_belatedPNG_0.0.8a-min.js',
                      '/lib/My97DatePicker/4.8/WdatePicker.js',
                      '/lib/ueditor/1.4.3/ueditor.config.js',
                      '/lib/ueditor/1.4.3/ueditor.all.min.js',
                      '/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js',
                      '/lib/jquery.validation/1.14.0/jquery.validate.js',
                      '/lib/jquery.validation/1.14.0/validate-methods.js',
                      '/lib/jquery.validation/1.14.0/messages_zh.js',
                      '/lib/webuploader/0.1.5/webuploader.min.js'
                  ]
              ];
              break;
      }   
       return array_merge_recursive($common,$info); 
    } 

    /**
     * 销毁,解除引用
     */
    public function destroy()
    {

    }

}
