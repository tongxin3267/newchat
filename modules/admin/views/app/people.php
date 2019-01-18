<!DOCTYPE html>
<html>
<head lang="en">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit" />
    <title>分销中心</title>
    <link href="<?= Yii::$app->request->baseUrl ?>/statics/recode/css/layui.css" rel="stylesheet">
    <link media="all" href="<?= Yii::$app->request->baseUrl ?>/statics/app_recode/css/distributionlist.css" type="text/css" rel="stylesheet" />
    <style>
        .layui-table th{
            text-align: center;
        }
    </style>
</head>
<body>
<div class="ny-main">
    <div class="ny-panel-body">
        <!-- 开通分销 -->
        <div class="open-cps-outer">
            <div class="open-cps-title margin-bottom-10">
                代理分销
            </div>
            <div class="open-cps-subtitle margin-bottom-20">
                代理分销是微帮科技为广大用户推出的福利，请复制下面的推广链接，发送到qq群微信，为您增加免费使用微帮小程序的使用时间和源源不断的收益。
                <a href="/" target="_top">了解分销收益详情&gt;</a>
            </div>
            <div>
                <input class="ny-input-reset " id="input" style="width:500px  " value=<?php echo $url ?> />
                <input type="button" class="ny-btn btn-primary" onclick="copyUrl()" value="复制推广链接" />
            </div>
            <script>
                function copyUrl() {
                    var Url=document.getElementById("input");
                    Url.select(); // 选择对象
                    document.execCommand("Copy"); // 执行浏览器复制命令
                    alert("复制成功！发送给朋友吧！");
                }


            </script>
        </div>
    </div>
    <div id="contentBox">
        <div class="ny-panel">
            <div class="ny-panel-heading">
                <div class="ny-panel-title">
                    分销列表
                </div>
            </div>
            <div class="ny-panel-body">
                <div class="margin-bottom-20 clearfix">
                    <a href="javascript:" target="_top" class="pull-left ny-btn btn-primary a " style="margin-right:20px" id="user">我的下级</a>
                    <a href="javascript:" target="_top"  class="pull-left ny-btn " style="margin-right:20px" id="xcx" >下级小程序发布记录</a>
                    <a href="javascript:" target="_top" class="pull-left ny-btn " id="change">下级充值记录</a>
                    <div class="pull-left clearfix searchs" style="display: block">
                        <div data-field_name="searchType" id="nyDropdownContainer" class="btn-group ny-search-group pull-left margin-left-24">
                            <a type="button" href="javascript:" class="ny-btn"  style="width: 98px;border-color: #d6d6d6;"> <span id="">用户查找</span> </a>
                        </div>


                            <div class="pull-left  search-group-input-wrapper">
                                <input name="searchKey" class="ny-input-reset search-group-input search" placeholder="请输入用户名" value="" type="text" />
                                <span class="input-clear-icon"></span>
                            </div>
                            <input class="pull-left ny-btn btn-primary btn-primary-search" value="" type="submit" id="search"/>

                    </div>

                </div>

                <div class="table-space" style="height: 35px;"></div>
                <div style="width: 100%;padding-top: 10px">

                    <!--以下为两个必须div元素-->
                    <div id="table" style="text-align: center;"></div>
                     <div style="text-align: center; display: none;" id="nothing">暂无记录
                    <hr>
                	</div>
                    <div id="pageBar"></div>
                </div>
                <div class="alert-warn-lg  clearfix" style="margin-top:50px">
                    <!--<div class="ny-btn btn-primary-faker">发票申请说明：</div>-->
                    <div class="tip-title">
                        分销规则：
                    </div>
                    <ul class="ny-panel-list">
                        <li>1. 包年包月用户可开发票金额为订单实际结算金额（可累计多个订单）。</li>
                        <li>2. 最终解释权在法律允许范围内微帮所有。</li>
                        <li>3. 最终解释权在法律允许范围内微帮所有。</li>
                        <li>4. 最终解释权在法律允许范围内微帮所有。</li>
                        <li>5. <span class="text-stress">最终解释权在法律允许范围内微帮所有</span>，最终解释权在法律允许范围内微帮所有。</li>
                        <li>6. <span class="text-stress">最终解释权在法律允许范围内微帮所有。</span></li>
                        <li>7. 最终解释权在法律允许范围内微帮所有。</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript" src="<?= Yii::$app->request->baseUrl ?>/statics/recode/js/layui.js"></script>
<script type="text/javascript" src="<?= Yii::$app->request->baseUrl ?>/statics/recode/js/nicePage.js"></script>
<script type="text/javascript">
    //标准json格式 目前只支持[{a:b,c:d},{a:b,c:d}]此种格式
    var json = '';

    //nameList与widthList的数组长度要一致
    nameList = ['ID', '用户名','用户分享级别', '添加时间', '账号级别', '账号状态', '小程序发布状态'];
    widthList = [100, 100, 100, 100, 100, 100,100];

    $(function () {
        $.ajax({
            async: false,
            url: "index.php?r=admin%2Fapp%2Fpeople",
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

    $("#change").click(function(){
        $(".searchs").show();
      	$("#nothing").hide();
         $("#change").attr("class","pull-left ny-btn btn-primary a");
        $("#xcx").attr("class","pull-left ny-btn");
        $("#user").attr("class","pull-left ny-btn");
        $.ajax({
            async: false,
            url: "index.php?r=admin%2Fapp%2Fpeoples",
            type: "post",
            dataType: "json",
            data: {
                _csrf: _csrf,
            },
            success: function (res) {
                if(res.a){
                    nameList = ['ID', '用户名','交易金额', '交易内容', '一级提成','二级提成', '交易时间', '交易状态', '微信支付订单号','一级提现状态','二级提现状态'];
                    widthList = [80, 100, 100, 100, 100, 100, 120, 100,100, 100,100];
                    delete res.a;
                }else{
                    nameList = ['ID', '用户名','交易金额', '交易内容', '交易提成', '交易时间', '交易状态', '微信支付订单号','提现状态'];
                    widthList = [80, 100, 100, 100, 100, 120, 100, 100,100];
                }
               
                if(res.length == 0){
                    $("#nothing").show();
                    json = res;
                }else{
                    json = res;
                }
           
            }
        });

        $(".layui-laypage-btn").trigger("click");

    });

    $("#user").click(function(){
        $(".searchs").show();
      	$("#nothing").hide();
        $("#change").attr("class","pull-left ny-btn");
        $("#xcx").attr("class","pull-left ny-btn");
        $("#user").attr("class","pull-left ny-btn btn-primary a");
        nameList = ['ID', '用户名','用户分享级别', '添加时间', '账号级别', '账号状态', '小程序发布状态'];
        widthList = [100, 100, 100, 100, 100, 100,100];
        $.ajax({
            async: false,
            url: "index.php?r=admin%2Fapp%2Fpeople",
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
        $(".layui-laypage-btn").trigger("click");
    });

    $("#xcx").click(function(){
        $(".searchs").hide();
      	$("#nothing").hide();
        $("#change").attr("class","pull-left ny-btn");
        $("#xcx").attr("class","pull-left ny-btn  btn-primary a");
        $("#user").attr("class","pull-left ny-btn");
        nameList = ['ID', '用户名','小程序发布状态', '小程序发布时间','奖励领取'];
        widthList = [100, 100, 100, 100,100];
        $.ajax({
            async: false,
            url: "index.php?r=admin%2Fapp%2Fxcx",
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
        $(".layui-laypage-btn").trigger("click");
    });

    //点击搜索
    $("#search").click(function(){
		$("#nothing").hide();
        search = $(".search").val();
        table =  $(".a").text();

        if( table  == "我的下级"){
            url = "index.php?r=admin%2Fapp%2Fpeople";

        }
        if( table  == "下级充值记录"){
            url = "index.php?r=admin%2Fapp%2Fpeoples";

        }

        // nameList = ['ID', '用户名','小程序发布状态', '小程序发布时间'];
        // widthList = [100, 100, 100, 100];
        $.ajax({
            async: false,
            url: url,
            type: "post",
            dataType: "json",
            data: {
                _csrf: _csrf,
                search: search,
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
        $(".layui-laypage-btn").trigger("click");
    });


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