<?php  $this->insert('Public/Head',$staticOption); ?>

<body>
<article class="page-container">
	<form action="" method="post" class="form form-horizontal" id="form-member-add">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>分类名称：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="" placeholder="" id="category_name" name="category_name">
                <input type="hidden" value="" id="id" name="id">
			</div>
		</div>

		<div class="row cl channelId_div">
			<label class="form-label col-xs-4 col-sm-3">排序：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="" placeholder="排序" id="sort" name="sort">
			</div>
		</div>

		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>是否显示：</label>
			<div class="formControls col-xs-8 col-sm-9 skin-minimal">
					<div class="radio-box">
						<input type="radio" id="video-type-1" value="1" name="is_display" >
						<label for="video-type-1">显示</label>
					</div>
				<div class="radio-box">
					<input type="radio" id="video-type-0" value="0" name="is_display">
					<label for="video-type-0">不显示</label>
				</div>
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
    $(function() {
        $("#category_name").val("<?php echo isset($data['category_name']) ? $data['category_name'] : '' ?>");
        $("#sort").val("<?php echo isset($data['sort']) ? $data['sort'] : '' ?>");
        $("#id").val(<?php echo isset($data['id']) ? $data['id'] : '' ?>);
        <?php if(isset($data['is_display'])){ ?>
            $("#video-type-<?php echo $data['is_display']; ?>").attr("checked","checked");
        <?php } ?>

        $(".footer").hide();
        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });
        $("#form-member-add").validate({
            rules: {
                category_name: {
                    required: true,
                    minlength: 2,
                    maxlength: 40
                },
                sort: {
                    required: false,
                    min: 0
                },
                is_display: {
                    required: true,
                }
            },
            onkeyup: false,
            focusCleanup: true,
            success: "valid",
            submitHandler: function (form) {
                var index = parent.layer.getFrameIndex(window.name);
                $.ajax({
                    type: 'POST',
                    url: '/cate/save',
                    dataType: 'json',
                    data: $("#form-member-add").serialize(),
                    success: function (data) {
                        if (data.code == '1') {
                            layer.msg(data.message, {icon: 6, time: 1000});
                        } else {
                            layer.msg(data.message, {icon: 5, time: 1000});
                        }
                        setTimeout(function () {
                           // parent.location.replace('/cate/index');
                            parent.layer.close(index);
                        }, 1000);
                    },
                    error: function (data) {
                        layer.msg(data.message, {icon: 5, time: 1000});
                        console.log(data.message);
                    },
                });
            }
        })
    })
</script>
 </body>
</html>