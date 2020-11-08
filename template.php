<html>

<head>
    <link rel="stylesheet" href="<?=Typecho_Common::url("ShiroiRightKey/style/bootstrap.min.css", Helper::options()->pluginUrl)?>">
</head>

<body>
<div class="row">
    <div class="col-xs-12 col-md-10">
        <table id="rightTable" class="rightTable table table-condensed">
            <tr>
                <th>名称</th>
                <th>链接</th>
                <th>操作</th>
            </tr>
        </table>
        <input type="text" id="rightContent" name="rightContent" placeholder="内容">
        <input type="text" id="rightLink" name="rightLink" placeholder="链接地址">
        <button type="button" class="btn btn-success btn-sm"
                onclick="modifyRight($('#rightContent').val(),$('#rightLink').val())">修改
        </button>
        <button type="button" class="btn btn-success btn-sm"
                onclick="addRight($('#rightContent').val(),$('#rightLink').val())">新增
        </button>
    </div>
</div>
</body>

</html>
<script>
    var _thisTR = '';

    //删除
    function deleteRight(obj, id) {
        $(obj).parent().parent().remove();
        postData();
    }

    function modifyRight(content, link) {
        $(_thisTR).children().eq(0).html(content);
        $(_thisTR).children().eq(1).html(link);
        postData();
    }

    //新增
    function addRight(content, link, init = 0) {
        if (init == 0) {
            $('.rightTable').append("<tr><td>" + content + "</td><td>" + link + "</td><td>" + createButton() +
                "</td></tr>");
        } else {
            $("#rightContent").val(content);
            $("#rightLink").val(link);
            _thisTR = init;
        }
        postData();
    }

    //创建操作按钮
    function createButton(str = '') {
        str += '<button type="button" class="btn btn-danger" onclick="deleteRight(this)">删除</button>';
        str += '<button type="button" class="btn btn-primary" onclick="addRight($(this).parent().parent().children().eq(0).html()' +
            ',$(this).parent().parent().children().eq(1).html(),$(this).parent().parent())">修改</button>';
        return str;
    }

    //修改数据
    function postData() {
        let table = $(".rightTable").html();
        $.ajax({
            type:"POST",
            url:'<?=Typecho_Common::url("ShiroiRightKey/rightKey.php", Helper::options()->pluginUrl)?>',
            data:{element:`${table}`}
        });
    }

    //初始化参数
    window.onload = function(){
        $.ajax({
            type:"GET",
            dataType:"json",
            url:'<?=Typecho_Common::url("ShiroiRightKey/rightKey.php", Helper::options()->pluginUrl)?>',
            success:function (data) {
                for(let i in data) {
                    addRight(data[i][0],data[i][1]);
                }
            }
        });
    };
</script>