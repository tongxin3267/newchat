<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>NicePage demo</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link href="<?= Yii::$app->request->baseUrl ?>/statics/recode/css/layui.css" rel="stylesheet">
    <style>
        .layui-table th{
            text-align: center;
        }
    </style>

</head>

<BODY>
<center style="padding-top: 40px">
    <a style="font-size: 30px;font-weight: 500">充值记录</a>
    </br>
    <div style="width: 80%;padding-top: 20px">

        <!--以下为两个必须div元素-->
        <div id="table" style="text-align: center;"></div>
      	<div style="text-align: center; display: none;" id="nothing">暂无记录
                    <hr>
         </div>
        <div id="pageBar"></div>
    </div>

</center>
</body>

<script type="text/javascript" src="<?= Yii::$app->request->baseUrl ?>/statics/recode/js/layui.js"></script>
<script type="text/javascript" src="<?= Yii::$app->request->baseUrl ?>/statics/recode/js/nicePage.js"></script>
<script type="text/javascript">
    //标准json格式 目前只支持[{a:b,c:d},{a:b,c:d}]此种格式
    var json = '';

    //nameList与widthList的数组长度要一致
    var nameList = ['ID', '用户ID','金额(元)','用户名','交易内容','交易时间', '交易状态', '订单号'] //table的列名
    var widthList = [100, 100, 100, 100, 100, 100, 100, 100] //table每列的宽度

    $(function () {
        $.ajax({
            async: false,
            url: "index.php?r=admin%2Fapp%2Frecode",
            type: "post",
            dataType: "json",
            data: {
                _csrf: _csrf,
            },
            success: function (res) {
                  if(res.length == 0){
                    $("#nothing").show();
                    json = res;
                }else{
                    json = res;
                }

            }
        });


    });



    /**
     * 初始化设置nicepage组件    v1.0
     *-------------------------------------------------------------
     * 进行数据组装,与layui交互进行元素渲染
     *-------------------------------------------------------------
     * @param    {string}  table     table的div id
     * @param    {string}  bar     底部分页的div id
     * @param    {int}  limit     每页默认行数
     * @param    {string}  color     底部分页的颜色
     * @param    {array}  layout     底部分页的布局,具体可参考layui api
     *
     * @date     2018-10-19
     * @author   Thomas.dz <hzdz163@163.com>
     */

    $( function () {
        nicePage.setCfg({
            table: 'table',
            bar: 'pageBar',
            limit: 10,
            color: '#1E9FFF',
            layout: ['count', 'prev', 'page', 'next', 'limit', 'skip']
        });

    });//初始化完成
</script>

</html>