<?php  $this->insert('Public/Head',$staticOption); ?>
<body>
<input type="hidden" id="TenantId" name="TenantId" value="" />
<div class="header"></div>
<div class="loginWraper">
  <div id="loginform" class="loginBox">
    <form class="form form-horizontal"   method="post" id="form-admin-login">
      <div class="row cl">
        <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60d;</i></label>
        <div class="formControls col-xs-8">
          <input id="userName" name="userName" type="text" placeholder="账户" class="input-text size-L">
        </div>
      </div>
      <div class="row cl">
        <label class="form-label col-xs-3"><i class="Hui-iconfont">&#xe60e;</i></label>
        <div class="formControls col-xs-8">
          <input id="passWd" name="passWd" type="password" placeholder="密码" class="input-text size-L">
            <input type="hidden" value="<?php  echo $csrfToken; ?>" id="csrfToken" name="csrfToken">
        </div>
      </div>
      <div class="row cl">
        <div class="formControls col-xs-8 col-xs-offset-3">
          <input name="" type="submit" class="btn btn-success radius size-L" value="&nbsp;登&nbsp;&nbsp;&nbsp;&nbsp;录&nbsp;">
          <input name="" type="reset" class="btn btn-default radius size-L" value="&nbsp;取&nbsp;&nbsp;&nbsp;&nbsp;消&nbsp;">
        </div>
      </div>
    </form>
  </div>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->insert('Public/Footer',$staticOption); ?>

<script type="text/javascript">
  $(function() {
    $('.skin-minimal input').iCheck({
      checkboxClass: 'icheckbox-blue',
      radioClass: 'iradio-blue',
      increaseArea: '20%'
    });
    $("#form-admin-login").validate({
      rules: {
          userName: {
              required: true,
              minlength: 3
         },
          passWd: {
              required: true,
              minlength: 6
         }
      },
      messages: {
          userName: {
             required: "*请输入用户名",
             minlength:"用户名错误"
          },
          passWd: {
             required: "*请输入密码",
             minlength:"密码格式不正确"
          }
      },
      onkeyup: false,
      focusCleanup: true,
      success: "valid",
      submitHandler: function (form) {
        var index = parent.layer.getFrameIndex(window.name);
        $.ajax({
          type: 'POST',
          url: '/admin/login',
          dataType: 'json',
          data: $("#form-admin-login").serialize(),
          headers: {
             'X-CSRF-TOKEN': $('#csrfToken').val()
          },
          success: function (data) {
            if (data.code == '1') {
              layer.msg(data.message, {icon: 6, time: 1000});
              setTimeout(function () {
                   top.location.href='/';
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