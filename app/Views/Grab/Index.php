<?php  $this->insert('Public/Head',$staticOption); ?> 
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a href="/">首页</a> <span class="c-gray en">&gt;</span> <a href="/">抓取管理</a> <span class="c-gray en">&gt;</span> 抓取列表 <a class=" btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont btn-refresh">&#xe68f;</i></a></nav>
<div class="page-container">
  <div class="text-c">
    视频分类:<span class="select-box inline">
		     <select name="videoType" id="videoType" class="select">
                 <option value="0">--全部--</option>
                <?php foreach($videotype as $key => $val){ ?>
                    <option value="<?php echo $val['id']; ?>"><?php echo $val['type']; ?></option>
                 <?php } ?>
           </select>
      </span>
      抓取地址: <input type="text" class="input-text" style="width:250px" placeholder="输入抓取地址" id="grab_address" name="grab_address">
    <button type="submit" class="btn btn-success radius" id="btn-search" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
  </div>
  <div class="cl pd-5 bg-1 bk-gray mt-20">
       <span class="l">
           <a href="javascript:;" onclick="datadel('grap')" class="btn btn-danger radius delAll"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
           <a href="javascript:;" onclick="grab_add('添加抓取任务','/grab/add','','510')"  class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加抓取事件</a>
       </span>
  </div>
  <div class="mt-20">
  <table class="table table-border table-bordered table-hover table-bg table-sort">
    <thead>
      <tr class="text-c">
        <th width="25"><input type="checkbox" name="select-all" value="0" id="select-all"></th>
        <th width="120">抓取说明</th>
        <th width="60">视频类别</th>
        <th width="50">分类</th>
        <th width="70">类型</th>
        <th width="210">抓取地址</th>
        <th width="60">当前状态</th>
        <th width="50">成功数</th>
        <th width="50">失败数</th>
        <th width="110">最后执行时间</th>
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
    /**
     * 搜索事件
     */
    $("#btn-search").click(function () {
        $(".table-sort").dataTable().fnDestroy();
        refreshDataTable();
    });
})

/**
 * 执行事件
 * @param id
 */
function execData(id,type){
    layer.confirm('确认要执行吗？', function () {
        $.ajax({
            type: 'POST',
            url: '/grab/implement',
            data: {'id': id, 'type': type},
            dataType: 'json',
            success: function (data) {
                layer.msg(data.msg, {icon: 1, time: 1000});
                $(".table-sort").dataTable().fnDraw(true);
            },
            error: function (data) {
                console.log(data.msg);
            },
        });
    });
}



/**
 * 删除事件
 * @param id
 */
function delData(id) {
    layer.confirm('确认要删除吗？', function () {
        $.ajax({
            type: 'POST',
            url: '/grab/del/?id=' + id,
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
function grab_add(title,url,w,h){
    layer_show(title,url,w,h);
}

/**
 * dataselect 列表函数
 */
function refreshDataTable() {
    var url = '/grab/index';
    var grab_address = $("#grab_address").val();
    var videoType = $("#videoType").val();
    if(typeof(grab_address) == 'undefined'){
        grab_address = '';
    }
    if(typeof(videoType) == 'undefined'){
        videoType = '';
    }
    url += "/?grab_address="+grab_address+"&videoType="+videoType;
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
//            "columns": [
//                { "data": "id" },
//                { "data": "user_id" },
//                { "data": "channelId" },
//                { "data": "fail_number" },
//                { "data": "grab_title" }
//            ]

        "aoColumns" : [//初始化要显示的列
            {
                "mDataProp" : "id",//获取列数据，跟服务器返回字段一致
                "sClass" : "center",//显示样式
                "mRender" : function(data, type, full) {
                    return "<label><input type='checkbox' class='ace checkbox_select' name='chkId' value='"+data+"' /><span class='lbl'></span></label>"
                }
            },
            {
                "mDataProp" : "grab_title"
            },
            {
                "mDataProp" : "videoTypeName"
            },
            {
                "mDataProp" : "category"
            },
            {
                "mDataProp" : "typeName"
            },
            {
                "mDataProp" : "grab_address",
                "mRender" : function(data, type, full) {
                    return "<a href='"+data+"' target='_blank' />"+data+"</a>"
                }
            },
            {
                "mDataProp" : "statusText"
            },
            {
                "mDataProp" : "success_number"
            },
            {
                "mDataProp" : "fail_number"
            },
            {
                "mDataProp" : "exec_date"
            },
            {
                "mDataProp" : "operation"
            }
        ],
        "aoColumnDefs": [
//        //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
          {"orderable":false,"aTargets":[0,1,10]}// 制定列不参与排序
      ]
    });
};

</script> 
</body>
</html>