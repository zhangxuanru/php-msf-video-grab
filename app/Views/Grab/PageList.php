<?php  $this->insert('Public/Head',$staticOption); ?>
<body>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a href="/">首页</a> <span class="c-gray en">&gt;</span> <a href="/">抓取管理</a> <span class="c-gray en">&gt;</span> <a href="/">抓取列表</a>  <span class="c-gray en">&gt;</span> 抓取详情  <a class=" btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont btn-refresh">&#xe68f;</i></a></nav>
<div class="page-container">
    <div class="mt-20">
        <table class="table table-border table-bordered table-hover table-bg table-sort">
            <thead>
            <tr class="text-c">
                <th width="25"><input type="checkbox" name="select-all" value="0" id="select-all"></th>
                <th width="50">ID</th>
                <th width="100">AV_ID</th>
                <th width="100">视频标题</th>
                <th width="100">视频源地址</th>
                <th width="90">文件路径</th>
                <th width="50">浏览总数</th>
                <th width="80">渠道标题</th>
                <th width="50">播放时长</th>
                <th width="100">上传七牛</th>
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
     * dataselect 列表函数
     */
    function refreshDataTable() {
        var url = '/grab/pageList/?id=<?php echo $id; ?>&execid=<?php echo $execId; ?>&type=<?php echo $type; ?>';
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
                    "mDataProp" : "id"
                },
                {
                    "mDataProp" : "av_id"
                },
                {
                    "mDataProp" : "title"
                },
                {
                    "mDataProp" : "grab_address",
                    "mRender" : function(data, type, full) {
                        return   '<a href="'+data+'" target="_blank">'+data+'</a>';
                    }
                },
                {
                    "mDataProp" : "filename"
                },
                {
                    "mDataProp" : "view_count"
                },
                {
                    "mDataProp" : "channel_title"
                },
                {
                    "mDataProp" : "length_seconds",
                    "mRender" : function(data, type, full) {
                        return  data+"秒";
                    }
                },
                {
                    "mDataProp" : "qiniu_upload",
                    "mRender" : function(data, type, full) {
                        return  (data == '1') ? '是' : '否';
                    }
                },
                {
                    "mDataProp" : "author",
                    "mRender" : function(data, type, full) {
                        return   '<a target="_blank" href="/video/detail/?info_id='+full.info_id+'&vid='+full.video_id+'">查看</a>';
                    }
                }
            ],
            "aoColumnDefs": [
                 {"orderable":false,"aTargets":[0,1,2,3,4,5,7,10]}// 制定列不参与排序
            ]
        });
    };

</script>
</body>
</html>