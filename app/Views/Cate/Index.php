<?php  $this->insert('Public/Head',$staticOption); ?>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a href="/"> 首页</a> <span class="c-gray en">&gt;</span> <a href="/">分类管理</a> <span class="c-gray en">&gt;</span> 分类列表 <?php if($id > 0){ ?><span class="c-gray en">&gt;</span> 子分类列表 <?php } ?><a class=" btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont btn-refresh">&#xe68f;</i></a></nav>
<div class="page-container">
    <div class="cl pd-5 bg-1 bk-gray mt-20">
       <span class="l">
           <a href="javascript:;" onclick="datadel('cate')" class="btn btn-danger radius delAll"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
           <a href="javascript:;" onclick="cate_add('添加分类','/cate/add','','510')"  class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加分类</a>
       </span>
    </div>
    <div class="mt-20">
        <table class="table table-border table-bordered table-hover table-bg table-sort">
            <thead>
            <tr class="text-c">
                <th width="25"><input type="checkbox" name="select-all" value="0" id="select-all"></th>
                <th width="100">分类名称</th>
                <th width="100">视频总数</th>
                <th width="100">分类总数</th>
                <th width="60">是否显示</th>
                <th width="50">排序</th>
                <th width="50">添加时间</th>
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
                url: '/cate/del/?id='+id,
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

    /*-添加--弹窗*/
    function cate_add(title,url,w,h){
        layer_show(title,url,w,h);
    }

    /**
     * dataselect 列表函数
     */
    function refreshDataTable() {
        var url = '/cate/index/?id=<?php echo $id; ?>';
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
                    "mDataProp" : "category_name",
                    "mRender" : function(data, type, full) {
                        var fid = full.id;
                        if(full.pid == '0'){
                          return `<a href="/cate/index/?id=${fid}" />${data}</a> `;
                        }else{
                            return data;
                        }
                    }
                },
                {
                    "mDataProp" : "video_count"
                },
                {
                    "mDataProp" : "cat_count"
                },
                {
                    "mDataProp" : "is_display",
                    "mRender" : function(data, type, full) {
                        return (data == '1') ? '是' : '否';
                    }
                },
//                {
//                    "mDataProp" : "type"
//                },
                {
                    "mDataProp" : "sort"
                },
                {
                    "mDataProp" : "addDate"
                },
                {
                    "mDataProp" : "pid",
                    "sClass" : "center",//显示样式
                    "mRender" : function(data, type, full) {
                        var fid = full.id;
                        return `<a onclick="cate_add('修改分类','/cate/add/?id=${fid}','','510')"/>编辑</a> &nbsp;&nbsp;&nbsp;&nbsp;
                        <a href='javascript:;' data-id='"+full.id+"' class='del' onclick='del(${fid})' />删除</a>`;
                    }
                }
            ],
            "aoColumnDefs": [
//        //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
                {"orderable":false,"aTargets":[0,1,2,3]}// 制定列不参与排序
            ]
        });
    };

</script>
</body>
</html>