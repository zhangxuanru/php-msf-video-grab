<?php  $this->insert('Public/Head',$staticOption); ?>

<body>
<article class="page-container">
	<form action="" method="post" class="form form-horizontal" id="form-member-add">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>所属类别：</label>
            <div class="formControls col-xs-8 col-sm-9 skin-minimal">
                <?php foreach ($videotype as $key => $value) { ?>
                    <div class="radio-box">
                        <input type="radio" id="video-type-<?php echo $key; ?>" value="<?php echo $key; ?>" name="video_type"  <?php if($key == '1'){ echo 'checked';} ?>>
                        <label for="video-type-<?php echo $key; ?>"><?php echo $value['type']; ?></label>
                    </div>
                <?php }?>
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>所属分类：</label>
            <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
				<select class="select" size="1" name="category">
                    <option value="0">选择分类</option>
                    <?php foreach ($catList as $key => $value) { ?>
                        <option value="<?php echo $key ?>"><?php echo $value; ?></option>
                    <?php  } ?>
                </select>
				</span> </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>抓取类型：</label>
            <div class="formControls col-xs-8 col-sm-9 skin-minimal">
                <?php foreach ($typeList as $key => $value) { ?>
                    <div class="radio-box">
                        <input type="radio" id="type-<?php echo $key; ?>" value="<?php echo $key; ?>" name="type"  <?php if($key == '1'){ echo 'checked';} ?>>
                        <label for="type-<?php echo $key; ?>"><?php echo $value; ?>
                         <?php echo  ($key == '3') ? '&nbsp;&nbsp;<span style="color:red">计划任务每天凌晨0:00执行</span>' : ''; ?>
                        </label>
                    </div>
                <?php }?>
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>抓取说明：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="" id="grab_title" name="grab_title">
            </div>
        </div>

		<div class="row cl channelId_div">
			<label class="form-label col-xs-4 col-sm-3">频道ID：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="" placeholder="YOUTUBE频道ID" id="channelId" name="channelId">
			</div>
		</div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>抓取地址：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="B站页面抓取目前只支持排行榜的页面" id="grab_address" name="grab_address">
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>抓取个数：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="" id="grabnum" name="grabnum">
            </div>
        </div>

		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
			</div>
		</div>
	</form>
</article>

<!--_footer 作为公共模版分离出去-->
<?php $this->insert('Public/Footer',$staticOption); ?>
<!--/_footer 作为公共模版分离出去-->

<script type="text/javascript">
	$(function(){
        $(".footer").hide();
		$('.skin-minimal input').iCheck({
			checkboxClass: 'icheckbox-blue',
			radioClass: 'iradio-blue',
			increaseArea: '20%'
		});

        //ifCreated 事件应该在插件初始化之前绑定
        $('input').on('ifChecked', function(event){
            var inputName = event.target.name;
            var inputVal  = event.target.defaultValue;
            if(inputName == 'video_type'){
                if(inputVal == '2'){
                   $(".channelId_div").hide();
                }else{
                    $(".channelId_div").show();
               }
            }
        });

        $("#form-member-add").validate({
			rules:{
                grabnum:{
					required:true,
                    min:1
				},
                grab_address:{
                    required:false,
                },
                grab_title:{
                    required:true,
                    minlength:2,
                    maxlength:50
                },
                channelId:{
                    required:false,
                },
                type:{
                    required:true,
                },
                category:{
					required:true,
                    min:1
				},
                video_type:{
                    required:true
                }
			},
            messages: {
                category: {
                    required: "请选择分类",
                    min:$.validator.format("请选择分类")
                }
            },
			onkeyup:false,
			focusCleanup:true,
			success:"valid",
			submitHandler:function(form){
				var index = parent.layer.getFrameIndex(window.name);
                $.ajax({
                    type: 'POST',
                    url: '/grab/save',
                    dataType: 'json',
                    data: $("#form-member-add").serialize(),
                    success: function (data) {
                        if(data.code == '1'){
                           layer.msg(data.message, {icon: 6, time: 1000});
                         }else{
                            layer.msg(data.message, {icon: 5, time: 1000});
                         }
                         setTimeout(function(){
                             parent.location.replace('/grab/index');
                             parent.layer.close(index);
                         },1000);
                    },
                    error: function (data) {
                        layer.msg(data.message, {icon: 5, time: 1000});
                        console.log(data.message);
                    },
                });
			}
		});
	});

</script>
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>