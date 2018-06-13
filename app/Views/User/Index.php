<?php  $this->insert('Public/Head',$staticOption); ?>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a href="/"> 首页</a> <span class="c-gray en">&gt;</span> <a href="/">管理员管理</a> <span class="c-gray en">&gt;</span> 管理员列表 <a class=" btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont btn-refresh">&#xe68f;</i></a></nav>
<div class="page-container">
    <div class="cl pd-5 bg-1 bk-gray mt-20">
       <span class="l">
           <a href="javascript:;" onclick="datadel('admin')" class="btn btn-danger radius delAll"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
           <a href="javascript:;" onclick="cate_add('添加管理员','/user/add')"  class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加管理员</a>
       </span>
    </div>
    <div class="mt-20">
        <table class="table table-border table-bordered table-hover table-bg table-sort">
            <thead>
            <tr class="text-c">
                <th width="25"><input type="checkbox" name="select-all" value="0" id="select-all"></th>
                <th width="40">ID</th>
                <th width="150">登录名</th>
                <th width="90">手机</th>
                <th width="150">邮箱</th>
                <th>角色</th>
                <th width="130">加入时间</th>
                <th width="100">是否已启用</th>
                <th width="100">操作</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<!--_footer 作为公共模版分离出去-->
<?php $this->insert('Public/Footer',$staticOption); ?>
</body>
</html>

<script type="text/javascript">
    $(function() {
        refreshDataTable();
    })

    /**
     * 删除事件
     * @param id
     */
  function del(id){
        layer.confirm('确认要删除吗？', function () {
            $.ajax({
                type: 'POST',
                url: '/user/del/?id='+id,
                dataType: 'json',
                success: function (data) {
                    layer.msg(data.message, {icon: 1, time: 1000});
                    $(".table-sort").dataTable().fnDraw(true);
                },
                error: function (data) {
                    console.log(data.msg);
                },
            });
        });
    }

    /**
     * 修改状态
     * */
    function upStart(id,type)
    {
        $.ajax({
            type: 'POST',
            url: '/user/updatestart/?type='+type+'&id='+id,
            dataType: 'json',
            success: function (data) {
                layer.msg(data.message, {icon: 1, time: 1000});
                $(".table-sort").dataTable().fnDraw(true);
            },
            error: function (data) {
                layer.msg(data.message, {icon: 1, time: 1000});
                console.log(data.msg);
            },
        });
    }


    /*-添加--弹窗*/
    function cate_add(title,url,w,h){
        layer_show(title,url,w,h);
    }

    /**
     * dataselect 列表函数
     */
    function refreshDataTable() {
        var url = '/user/';
        var table = $('.table-sort').DataTable({
            "aaSorting": [[ 0, "desc" ]],//默认第几个排序
            "sPaginationType": "full_numbers",
            "bPaginite": true,
            "bInfo": true,
            "bSort": true,
            "processing": false,
            "serverSide": true,
            "searching" : false, //去掉搜索框方法一
            "sAjaxSource": url,//这个是请求的地址
            "fnServerData": retrieveData,
            "aoColumns" : [//初始化要显示的列
                {
                    "mDataProp" : "id",//获取列数据，跟服务器返回字段一致
                    "sClass" : "center",//显示样式
                    "mRender" : function(data, type, full) {
                        return "<label><input type='checkbox' class='ace checkbox_select' name='chkId' value='"+data+"' /><span class='lbl'></span></label>"
                    }
                },
                {
                    "mDataProp" : "id",
                },
                {
                    "mDataProp" : "userName",
                },
                {
                    "mDataProp" : "phone"
                },
                {
                    "mDataProp" : "email"
                },
                {
                    "mDataProp" : "roleName",
               },
                {
                    "mDataProp" : "addDate",
                },
                {
                    "mDataProp" : "disable",
                    "mRender" : function(data, type, full) {
                          return data == '0' ? '启用' : '禁用';
                    }
                },
                {
                    "mDataProp" : "pid",
                    "sClass" : "center",//显示样式
                    "mRender" : function(data, type, full) {
                        var fid = full.id;
                        var d = '1';
                        var startText = '禁用';
                        if(full.disable == '1'){
                            startText = '启用';
                            d = 0;
                        }
                        return `<a href='javascript:;' data-id='"+full.id+"' class='del' onclick='upStart(${fid},${d})' />${startText}</a> &nbsp; <a onclick="cate_add('修改用户','/user/add/?id=${fid}')"/>编辑</a> &nbsp;
                        <a href='javascript:;' data-id='"+full.id+"' class='del' onclick='del(${fid})' />删除</a>`;
                    }
                }
            ],
            "aoColumnDefs": [
                 {"orderable":false,"aTargets":[0,1,2,3,4,5,6,7]}// 制定列不参与排序
            ]
        });
    };
</script>
</body>
</html>