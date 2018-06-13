<?php $this->insert('Public/Head',$staticOption);
  $rData = function($prefix,$key = null) use($data){
    if(is_null($key)){
        $val = isset($data[$prefix]) ? $data[$prefix] : 0;
    }else{
        $val = isset($data[$prefix]) && isset($data[$prefix][$key]) ? $data[$prefix][$key] : 0;
    }
    return $val;
};
?>
<body>
<div class="page-container">
	<form action="" method="post" class="form form-horizontal" id="form-video-edit">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>视频标题：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="<?php echo $rData('videoInfo','title'); ?>" placeholder="视频标题" id="title" name="title">
			</div>
		</div>

		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">关键字：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" name="keywords" id="keywords" placeholder="关键字" value="<?php echo $rData('videoInfo','keywords'); ?>" class="input-text">
			</div>
		</div>


		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">浏览次数：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" name="view_count" id="view_count" placeholder="浏览次数" value="<?php echo $rData('videoInfo','view_count'); ?>" class="input-text">
			</div>
		</div>

		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">评论次数：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" name="reviews_number" id="reviews_number" placeholder="评论次数" value="<?php echo $rData('videoInfo','reviews_number'); ?>" class="input-text">
			</div>
		</div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">喜欢次数：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" name="like_number" id="like_number" placeholder="喜欢次数" value="<?php echo $rData('videoInfo','like_number'); ?>" class="input-text">
            </div>
        </div>


		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">视频HLS地址：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="<?php echo $rData('videoInfo','hls_key'); ?>" placeholder="视频HLS地址" id="hls_key" name="hls_key">
			</div>
		</div>

		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">视频分类：</label>
			<div class="formControls col-xs-8 col-sm-9"> <span class="select-box inline">
                     <select name="catData" id="catData" class="select" onchange="getsubCatData()">
				   <?php foreach($catData as $key => $val){ ?>
                       <option value="<?php echo $val['id']; ?>"><?php echo $val['category_name']; ?></option>
                   <?php } ?>
                     </select>
				</span>   &nbsp;&nbsp;<span class="select-box inline" id="subClass" style="display: none">
		     <select name="catSubData" id="catSubData" class="select" >
                 <option value="0">--全部--</option>
             </select>
      </span>&nbsp;&nbsp; </div>
		</div>

		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">排序值：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="<?php echo $rData('videoInfo','sort'); ?>" placeholder="排序值" id="sort" name="sort">
			</div>
		</div>

		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">允许评论：</label>
			<div class="formControls col-xs-8 col-sm-9 skin-minimal">
				<div class="check-box">
					<input type="checkbox" id="is_reviews" name="is_reviews"  value="1" <?php echo $rData('videoInfo','is_reviews') == '1' ? 'checked' : ''; ?> >
					<label for="checkbox-1">&nbsp;</label>
				</div>
			</div>
		</div>

        <input type="hidden" value="<?php echo $rData('videoInfo','description'); ?>" name="operContent" id="operContent">
        <input type="hidden" value="<?php  echo $video_id; ?>" id="video_id" name="video_id">
        <input type="hidden" value="<?php  echo $info_id; ?>"  id="info_id"  name="info_id">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-2">详细内容：</label>
			<div class="formControls col-xs-8 col-sm-9"> 
				<script id="editor" type="text/plain" style="width:100%;height:400px;"></script> 
			</div>
		</div>

		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
				<button  class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
			</div>
		</div>
	</form>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->insert('Public/Footer',$staticOption); ?>
<script type="text/javascript">
    //获取子分类
    function getsubCatData()
    {
        var id = '<?php echo $rData('catgory','id') ?>';
        var classId = $('#catData').val();
        $.ajax({
            type: 'GET',
            url: '/cate/getsubclass',
            data: {'id': classId},
            dataType: 'json',
            success: function (data) {
                $("#catSubData option").remove();
                $.each(data,function(key,val){
                    if(id == val.id){
                        $("#catSubData").append("<option selected value='"+val.id+"'>"+val.category_name+"</option>");
                    }else{
                       $("#catSubData").append("<option value='"+val.id+"'>"+val.category_name+"</option>");
                    }
                })
                $("#subClass").show();
            },
            error: function (data) {
                $("#catSubData option").remove();
            },
        });
    }

$(function(){
    $("#catData").val("<?php echo $rData('catgory','pid') ?>");
    $("#catData").change();
	$('.skin-minimal input').iCheck({
		checkboxClass: 'icheckbox-blue',
		radioClass: 'iradio-blue',
		increaseArea: '20%'
	});
    $("#form-video-edit").validate({
        rules: {
            title: {
                required: true,
                minlength: 5,
                maxlength: 100
            },
            keywords: {
                required: false
            },
            subClass: {
                required: true
            },
            catData:{
                required: true
            },
            sort:{
                 min:0
            },
            editor:{
                required: true
            }
        },
        messages: {
            title: {
                required: "请输入视频标题",
                minlength: "内容太短",
            },
            keywords:{
                required: "请输入视频关键字",
            },
            catData:{
                required: "请选择分类",
            },
            sort:{
                min:"排序值不能小于0"
            },
            editor:{
                required:"详细内容不能为空"
            }
        },
        onkeyup: false,
        focusCleanup: true,
        success: "valid",
        submitHandler: function (form) {
            $.ajax({
                type: 'POST',
                url: '/video/save',
                dataType: 'json',
                data: $("#form-video-edit").serialize(),
                success: function (data) {
                    if (data.code == '1') {
                        layer.msg(data.message, {icon: 6, time: 2000});
                        setTimeout(function () {
                            self.location.replace('/video');
                        },2000);
                    } else {
                        layer.msg(data.message, {icon: 5, time: 2000});
                    }
                },
                error: function (data) {
                    layer.msg(data.message, {icon: 5, time: 1000});
                    console.log(data.message);
                },
            });
        }
    });

	var ue = UE.getEditor('editor');
        ue.ready(function(){//编辑器初始化完成再赋值
          var content = $('#operContent').val();
          ue.setContent(content);  //赋值给UEditor
    });
});
</script>
</body>
</html>