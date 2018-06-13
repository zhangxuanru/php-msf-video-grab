<?php  $this->insert('Public/Head',$staticOption); ?> 
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a href="/"> 首页</a> <span class="c-gray en">&gt;</span> <a href="/video/"> 视频管理</a> <span class="c-gray en">&gt;</span> 视频列表 <a class=" btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont btn-refresh">&#xe68f;</i></a></nav>
<div class="page-container">
  <div class="text-c">
      <input type="hidden" value="<?php echo  $catId;?>" id="catId">
    视频类别:<span class="select-box inline">
		     <select name="videoType" id="videoType" class="select">
                 <option value="0">--全部--</option>
                <?php foreach($videotype as $key => $val){ ?>
                    <option value="<?php echo $val['id']; ?>"><?php echo $val['type']; ?></option>
                 <?php } ?>
           </select>
      </span>

      视频标题: <input type="text" class="input-text" style="width:250px" placeholder="视频标题" id="video_title" name="video_title">
      &nbsp;&nbsp;
      <input type="checkbox" value="1" name="is_Top" id="is_Top">置顶&nbsp;
      <input type="checkbox" value="1" name="is_recommend" id="is_recommend">推荐&nbsp;
      <input type="checkbox" value="1" name="is_del" id="is_del">删除
      &nbsp;&nbsp;
    <button type="submit" class="btn btn-success radius" id="btn-search" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
  </div>
  <div class="cl pd-5 bg-1 bk-gray mt-20">
       <span class="l">
           <a href="javascript:;" onclick="datadel('video')" class="btn btn-danger radius delAll"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a>
           <a href="javascript:;" onclick="setState('status',1)" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量恢复</a>
           <a href="javascript:;" onclick="setState('recommend',1)" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量推荐</a>
           <a href="javascript:;" onclick="setState('top',1)" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量置顶</a>
           <a href="javascript:;" onclick="setState('top',0)" class="btn btn-danger radius delAll"><i class="Hui-iconfont">&#xe6e2;</i> 批量取消置顶</a>
           <a href="javascript:;" onclick="setState('recommend',0)" class="btn btn-danger radius delAll"><i class="Hui-iconfont">&#xe6e2;</i> 批量取消推荐</a>

       </span>
  </div>
  <div class="mt-20">
  <table class="table table-border table-bordered table-hover table-bg table-sort">
    <thead>
      <tr class="text-c">
        <th width="25"><input type="checkbox" name="select-all" value="0" id="select-all"></th>
        <th width="100">视频标题</th>
        <th width="60">视频类别</th>
        <th width="50">分类</th>
        <th width="50">推荐</th>
        <th width="50">置顶</th>
        <th width="90">播放次数</th>
        <th width="60">评论数</th>
        <th width="60">播放时长</th>
        <th width="50">文件大小</th>
        <th width="50">抓取时间</th>
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
 * 批量推荐
 */
function setState(type,val)
{
    var idStr = '';
    $('.checkbox_select').each(function () {
        if($(this).is(":checked")){
            idStr+=$(this).val()+",";
        }
    });
    if(idStr.length == 0){
        alert('请选择要操作的数据');
        return true;
    }
    var tipMsg = (val == '1') ? '' : '取消';
    if(type == 'top'){
        tipMsg+='置顶';
    }
    if(type == 'recommend'){
        tipMsg+='推荐';
    }
    if(type == 'status'){
        tipMsg = '恢复';
    }
    layer.confirm('确认要'+tipMsg+'吗？', function () {
        $.ajax({
        type: 'POST',
        url: '/video/setState',
        data: {'id': idStr,'type':type,'val':val},
        dataType: 'json',
        success: function (data) {
             if(data.code == '1'){
                layer.msg(data.message, {icon: 6, time: 2000});
                $(".table-sort").dataTable().fnDraw(true);
             }else{
                 layer.msg(data.message, {icon: 5, time: 2000});
             }
        },
        error: function (data) {
           console.log(data);
        },
    });
 });
}


/**
 * 设置状态事件
 * @param id
 */
function setStatus(id,val) {
    var msg = '确认要删除吗?';
    if(val == '1'){
        msg = '确认要恢复吗?';
    }
    layer.confirm(msg, function () {
        $.ajax({
            type: 'POST',
            url: '/video/setState',
            data: {'id': id,'type':'status','val':val},
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
 * dataselect 列表函数
 */
function refreshDataTable() {
    var url = '/video/catData';
    var queryStr = '';
    var video_title = $("#video_title").val();
    var videoType = $("#videoType").val();
    var catId = $("#catId").val();
    if(video_title.length > 0 ){
        queryStr+="&title="+video_title;
    }
    if(videoType.length > 0 ){
        queryStr+="&videoType="+videoType;
    }
    if($('#is_Top').is(':checked')) {
        queryStr+="&is_top=1";
    }
    if($('#is_recommend').is(':checked')) {
        queryStr+="&is_recommend=1";
    }
    if($('#is_del').is(':checked')){
        queryStr+="&is_del=1";
    }
    queryStr+="&catId="+catId;
    url += "/?"+queryStr
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
                "mDataProp" : "title",
                "mRender" : function(data, type, full) {
                    return `<a href='http://www.13520v.com/vwatch/1233' target='_blank'>${data}</a>` ;
                }
            },
            {
                "mDataProp" : "type"
            },
            {
                "mDataProp" : "category"
            },
            {
                "mDataProp" : "is_recommend",
                "mRender" : function(data, type, full) {
                    return data=='1' ? '已推荐' : '否';
                 }
            },
            {
                "mDataProp" : "is_top",
                "mRender" : function(data, type, full) {
                    return data=='1' ? '已置顶' : '否';
                }
            },
            {
                "mDataProp" : "view_count"
            },
            {
                "mDataProp" : "reviews_number"
            },
            {
                "mDataProp" : "length_seconds"
            },
            {
                "mDataProp" : "video_size"
            },
            {
                "mDataProp" : "addDate"
            },
            {
                "mDataProp" : "id",
                "mRender" : function(data, type, full) {
                    var fid = full.id;
                    var info_id = full.info_id;
                    var video_id = full.video_id;
                    var status = full.status;
                    var operHtml = `<a href='/video/edit/?vid=${fid}&info_id=${info_id}'/>编辑</a> &nbsp;&nbsp;
                            <a target="_blank" href="/video/detail/?info_id=${info_id}&vid=${video_id}">查看</a>&nbsp;&nbsp;`;
                    if(status == '1'){
                         operHtml+= `<a href='javascript:;' data-id='"+full.id+"' class='del' onclick='setStatus(${fid},0)' />删除</a>`;
                    }else{
                        operHtml+= `<a href='javascript:;' data-id='"+full.id+"' class='del' onclick='setStatus(${fid},1)' />恢复</a>`;
                    }
                    return operHtml;
                }
            }
        ],
        "aoColumnDefs": [
           {"orderable":false,"aTargets":[0,1,2,9]}// 制定列不参与排序
      ]
    });
};

</script> 
</body>
</html>