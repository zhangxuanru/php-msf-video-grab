<?php  $this->insert('Public/Head',$staticOption);
  $rData = function($key) use($row){
      if($key == 'cat_id'){
            return isset($row[$key]) ? explode(',',$row[$key]) : [];
      }
       return isset($row[$key]) ? $row[$key] : '';
  }
?>
<body>
<div class="page-container">
	<form action="" method="post" class="form form-horizontal" id="form-nav-add">
        <input type="hidden" value="<?php echo $rData('id') ?>"  id="rid" name="id">
		<div id="tab-category" class="HuiTab">
			<div class="tabBar cl">
				<span>基本设置</span>
			</div>
			<div class="tabCon">
				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-3">
						<span class="c-red">*</span>
						上级栏目：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<span class="select-box">
						<select class="select" id="pid" name="pid">
							<option value="0">顶级分类</option>
                            <?php foreach($navList as $key => $val){ ?>
                                <option  <?php if($rData('pid') == $val['id'] ) { echo "selected"; }?> value="<?php echo $val['id'] ?>"><?php echo $val['nav_name'] ?></option>
                           <?php  } ?>
						</select>
						</span>
					</div>
					<div class="col-3">
					</div>
				</div>

				<div class="row cl">
					<label class="form-label col-xs-4 col-sm-3">
						<span class="c-red">*</span>
						导航名称：</label>
					<div class="formControls col-xs-8 col-sm-9">
						<input type="text" class="input-text" value="<?php echo $rData('nav_name') ?>" placeholder="" id="nav_name" name="nav_name">
					</div>
					<div class="col-3">
					</div>
				</div>

                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3">链接地址：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="text" class="input-text" value="<?php echo $rData('url') ?>" id="url" name="url">
                    </div>
                    <div class="col-3">
                    </div>
                </div>

                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3">排序：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                        <input type="text" class="input-text" value="<?php echo $rData('sort') ?>" id="sort" name="sort">
                    </div>
                    <div class="col-3">
                    </div>
                </div>

                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3">显示区域：</label>
                    <div class="formControls col-xs-8 col-sm-9 skin-minimal">
                    <div class="radio-box">
                            <input type="radio" id="region" value="1"  name="region" <?php echo $rData('region') == '1' ? 'checked' : ''; ?>>
                            <label for="region-1">导航栏</label>
                        </div>

                        <div class="radio-box">
                            <input type="radio" id="region" value="2"  name="region" <?php echo $rData('region') == '2' ? 'checked' : ''; ?>>
                            <label for="region-2">左侧</label>
                        </div>
                        <div class="radio-box">
                            <input type="radio" id="video-type-0" value="3" name="region" <?php echo $rData('region') == '3' ? 'checked' : ''; ?>>
                            <label for="region-3">主区域</label>
                        </div>
                        <div class="radio-box">
                            <input type="radio" id="video-type-4" value="4" name="region" <?php echo $rData('region') == '4' ? 'checked' : ''; ?>>
                            <label for="region-4">右侧</label>
                        </div>
                    </div>
                </div>

                <div class="row cl">
                    <label class="form-label col-xs-4 col-sm-3">关联分类：</label>
                    <div class="formControls col-xs-8 col-sm-9">
                     <?php foreach($data as $key => $val){ ?>
                        <dl class="permission-list">
                            <dt>
                                <label>
                                    <input type="checkbox" disabled value="<?php echo $val['id'] ?>" name="user-Character-0" id="user-Character-0">
                                    <?php echo $val['category_name'] ?></label>
                            </dt>
                            <dd>
                        <dl class="cl permission-list2">
                            <?php foreach($val['subCatList'] as $k => $v){ ?>
                            <dt>
                                <label class="">
                                    <input type="checkbox"  <?php if(in_array($v['id'],$rData('cat_id'))){ echo "checked";} ?> value="<?php echo $v['id'] ?>" name="cat_id[]" id="cat_id">
                                    <?php echo $v['category_name']; ?></label>
                            </dt>
                            <?php } ?>
                        </dl>
                        </dd>
                        </dl>
                        <?php } ?>
                    </div>
                </div>
			</div>
		</div>
		<div class="row cl">
			<div class="col-9 col-offset-3">
				<input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
			</div>
		</div>
	</form>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->insert('Public/Footer',$staticOption); ?>


<script type="text/javascript">
$(function(){
	$('.skin-minimal input').iCheck({
		checkboxClass: 'icheckbox-blue',
		radioClass: 'iradio-blue',
		increaseArea: '20%'
	});

    $("#tab-category").Huitab({
        index:0
    });

    $("#form-nav-add").validate({
        rules:{
            nav_name:{
                required:true
            },
            url:{
                required:true,
            },
            sort:{
                required:true
            },
            cat_id:{
                required:true
            },
        },
        onkeyup:false,
        focusCleanup:true,
        success:"valid",
        submitHandler:function(form){
            var index = parent.layer.getFrameIndex(window.name);
            $.ajax({
                type: 'POST',
                url: '/nav/navsave',
                dataType: 'json',
                data: $("#form-nav-add").serialize(),
                success: function (data) {
                    if(data.code == '1'){
                        layer.msg(data.message, {icon: 6, time: 1000});
                    }else{
                        layer.msg(data.message, {icon: 5, time: 1000});
                    }
                    setTimeout(function(){
                        parent.location.replace('/nav/');
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