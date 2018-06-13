<?php  $this->insert('Public/Head',$staticOption);
 $rData = function($key) use ($data){
      return isset($data[$key]) ?  $data[$key] : '';
 }
?>
<body>
<article class="page-container">
    <form class="form form-horizontal" id="form-admin-add">
        <input type="hidden" value="<?php echo $rData('id') ?>" id="id" name="id">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>管理员：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo $rData('userName') ?>" placeholder="" id="userName" name="userName">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>初始密码：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="password" class="input-text" autocomplete="off" value="" placeholder="密码" id="passwd" name="passwd">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>确认密码：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="password" class="input-text" autocomplete="off"  placeholder="确认新密码" id="repasswd" name="repasswd">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>手机：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo $rData('phone') ?>" placeholder="" id="phone" name="phone">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>邮箱：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" placeholder="@" value="<?php echo $rData('email') ?>" name="email" id="email">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">角色：</label>
            <div class="formControls col-xs-8 col-sm-9"> <span class="select-box" style="width:150px;">
			<select class="select" name="role" size="1">
                <option value="0">超级管理员</option>
            </select>
			</span> </div>
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
        $(".footer").hide();
        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });
        $("#form-admin-add").validate({
            rules: {
                userName: {
                    required: true,
                },
                passwd: {
                <?php if (empty($rData('id'))){ ?>
                    required: true,
                    minlength: 6
                 <?php }else{ ?>
                    required: false,
                 <?php } ?>
                },
                repasswd:{
                 <?php if (empty($rData('id'))){ ?>
                    required: true,
                    minlength: 6,
                 <?php }else{ ?>
                    required: false,
                 <?php } ?>
                    equalTo: "#passwd"
                },
                phone: {
                    required: true,
                    isMobile : true
                }
            },
            messages: {
                userName: {
                    required: "*请输入用户名"
                },
                passwd: {
                    required: "*请输入密码",
                    minlength:"密码长度必须大于6位"
                },
                repasswd: {
                    required: "*请输入确认密码",
                    minlength:"*密码长度必须大于6位",
                    equalTo: "*确认密码不一致"
                },
                phone: {
                    required: "*请输入手机号",
                    isMobile : "请正确填写您的手机号码"
                },
            },
            onkeyup: false,
            focusCleanup: true,
            success: "valid",
            submitHandler: function (form) {
                var index = parent.layer.getFrameIndex(window.name);
                $.ajax({
                    type: 'POST',
                    url: '/user/save',
                    dataType: 'json',
                    data: $("#form-admin-add").serialize(),
                    success: function (data) {
                        if (data.code == '1') {
                            layer.msg(data.message, {icon: 6, time: 1000});
                             setTimeout(function () {
                                parent.layer.close(index);
                            }, 1000);
                        } else {
                            layer.msg(data.message, {icon: 5, time: 1000});
                        }
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