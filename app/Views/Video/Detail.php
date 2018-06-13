<?php  $this->insert('Public/Head',$staticOption);
    $rData = function($prefix,$key = null) use($data,$statusArr,$videoType){
    if(is_null($key)){
        $val = isset($data[$prefix]) ? $data[$prefix] : 0;
    }else{
        $val = isset($data[$prefix]) && isset($data[$prefix][$key]) ? $data[$prefix][$key] : 0;
    }
    if($prefix == 'rows' && $key == 'status'){
        return $statusArr[$val];
    }
    if($prefix == 'rows' && $key == 'video_type'){
        return $videoType[$val]['type'];
    }
    if($prefix == 'log' && $key == 'content'){
        $info = json_decode($val,true);
        return $info;
    }
    if($prefix == 'videoInfo' && $key == 'filename'){
        if(!empty($val)){
            return  pathinfo($val,PATHINFO_FILENAME ).'.'.pathinfo($val,PATHINFO_EXTENSION );
        }else{
            return '';
        }
    }
    if($prefix == 'videoInfo' && $key == 'video_size'){
        return number_format($val/1024/1024,2) .'M';
    }
    return $val;
};
?>
<style>
    .imagelist li{
        float: left;
        width: 280px;
        height: 380px;
        margin-left: 20px;
    }
    .text-r{
        width: 300px;
    }
</style>

<body>
<div class="pd-20">
    <table class="table">
        <tbody>
        <tr>
            <th class="text-r" width="80">抓取地址：</th>
            <td><a href="<?php echo $rData('log','grab_address'); ?>" target="_blank"> <?php echo $rData('log','grab_address'); ?></a></td>
        </tr>
        <tr>
            <th class="text-r">执行状态：</th>
            <td><?php echo $rData('rows','status'); ?></td>
        </tr>
        <?php if( $data['rows']['status'] == '3'){ ?>
            <tr>
                <th class="text-r">失败原因：</th>
                <td> <?php echo $rData('log','content'); ?></td>
            </tr>
        <?php } ?>
        <tr>
            <th class="text-r">视频类别：</th>
            <td><?php echo $rData('rows','video_type'); ?></td>
        </tr>
        <tr>
            <th class="text-r">视频分类：</th>
            <td><?php echo $rData('pre_catgory','category_name'); ?></td>
        </tr>

        <tr>
            <th class="text-r" ><h4>视频信息：</h4></th>
            <td></td>
        </tr>

        <tr>
            <th class="text-r">视频标题：</th>
            <td><?php echo $rData('videoInfo','title'); ?></td>
        </tr>

        <tr>
            <th class="text-r">视频文件名：</th>
            <td><?php echo $rData('videoInfo','filename'); ?>
                &nbsp;&nbsp;&nbsp;&nbsp; <input type="button" value="点击播放" onclick="openvideo()"></td>
        </tr>

        <tr>
            <th class="text-r">视频HLS：</th>
            <td><?php echo $rData('videoInfo','hls_key'); ?></td>
        </tr>


        <tr>
            <th class="text-r">视频分类：</th>
            <td><?php echo $rData('catgory','category_name'); ?></td>
        </tr>

        <tr>
            <th class="text-r">YOUTUBE视频频道ID：</th>
            <td><?php echo $rData('catgory','channelId'); ?></td>
        </tr>

        <tr>
            <th class="text-r">浏览次数：</th>
            <td><?php echo $rData('videoInfo','view_count'); ?></td>
        </tr>

        <tr>
            <th class="text-r">评论次数：</th>
            <td><?php echo $rData('videoInfo','reviews_number'); ?></td>
        </tr>

        <tr>
            <th class="text-r">喜欢次数：</th>
            <td><?php echo $rData('videoInfo','like_number'); ?></td>
        </tr>


        <tr>
            <th class="text-r">关键字：</th>
            <td><?php echo $rData('videoInfo','keywords'); ?></td>
        </tr>

        <tr>
            <th class="text-r">详细说明：</th>
            <td><?php echo $rData('videoInfo','description'); ?></td>
        </tr>

        <tr>
            <th class="text-r">频道ID：</th>
            <td><?php echo $rData('videoInfo','channel_id'); ?></td>
        </tr>

        <tr>
            <th class="text-r">频道标题：</th>
            <td><?php echo $rData('videoInfo','channel_title'); ?></td>
        </tr>

        <tr>
            <th class="text-r">视频上传时间：</th>
            <td><?php echo $rData('videoInfo','published_at'); ?></td>
        </tr>

        <tr>
            <th class="text-r">播放时长：</th>
            <td> <?php echo $rData('videoInfo','length_seconds'); ?>秒</td>
        </tr>

        <tr>
            <th class="text-r">文件大小：</th>
            <td><?php echo $rData('videoInfo','video_size'); ?></td>
        </tr>

        <tr>
            <th class="text-r">是否上传七牛：</th>
            <td> <?php echo $rData('videoInfo','qiniu_upload') == '1' ? '是' :'否'; ?></td>
        </tr>

        <tr>
            <th class="text-r">视频标签：</th>
            <td><?php  echo is_array($rData('tags')) ? implode(',',$rData('tags')) : ''; ?></td>
        </tr>
        <tr>
            <th class="text-r">INFO数据集：</th>
            <td> <textarea rows="50" cols="120">
                    <?php print_r($rData('log','content')); ?>
                 </textarea>
            </td>
        </tr>

        <tr>
            <th class="text-r" ><h4>视频图片信息：</h4></th>
            <td></td>
        </tr>

        <tr>
            <td colspan="2" class="imagelist">
                <ul>
                    <?php foreach($rData('images') as $key => $val){ ?>
                        <li>
                            <dl>
                                <dd><a href="<?php echo $imagesDomain.'/'.$val['fillename'];?>" target="_blank"> <img src="<?php echo $imagesDomain.'/'.$val['fillename'];?>" width="120"> </a></dd>
                            </dl>
                            <br>
                            <dl>
                                <dd>网络原图:<input type="text" width="40" value="<?php echo $val['img_source_url']?>"></dd><br>
                                <dd>本地文件名:<?php echo  $val['fillename'];?></dd><br>
                                <dd>宽度:<?php echo  $val['width'];?></dd><br>
                                <dd>高度:<?php echo  $val['height'];?></dd><br>
                                <dd>封面图:<?php echo  $val['is_cover'] == '1' ? '是' : '否';?></dd><br>
                                <dd>上传七牛:<?php echo  $val['qiniu_upload'] == '1' ? '是' :'否';?></dd>
                            </dl>
                        </li>
                    <?php } ?>
                </ul>
            </td>
        </tr>
     </tbody>
  </table>
</div>

<?php $this->insert('Public/Footer',$staticOption); ?>
<script>
    function openvideo()
    {
        var url = "<?php echo $videoDomain.'/'.$rData('videoInfo','filename');?>";
        window.open(url);
    }
</script>


</body>
</html>