<!DOCTYPE html>
<html>
<head lang="en">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit" />
    <title>提现中心</title>
    <link href="<?= Yii::$app->request->baseUrl ?>/statics/recode/css/layui.css" rel="stylesheet">
    <link media="all" href="<?= Yii::$app->request->baseUrl ?>/statics/app_recode/css/withdraw.css" type="text/css" rel="stylesheet" />
    <style>
        .layui-table th{
            text-align: center;
        }
    </style>
</head>
<body>
<div class="ny-main">
    <div id="contentBox">
        <div class="ny-panel" style="display: block;" id="tixian">
            <div class="ny-panel-heading">
                <div class="ny-panel-title">
                    提现
                </div>
            </div>
            <div class="ny-panel-body">
                <div class="alert-warn margin-bottom-20">
                    申请提现后，您的款项将在3个工作日内按照后进先出的原则退回至您的收款账户，请耐心等待。
                </div>
                <div class="payments-block margin-bottom-20">
                    <form id="paymentForm" action="" method="post" target="_blank" class="form-horizontal ny-form">
                        <div class="form-group">
                            <div class="ny-control-label ny-label  ny-label-darken">
                                可提现金额：
                            </div>
                            <div class="col-xs-8 ny-form-control ny-input-group validate-control">
                                <span class="balance-int text-stress" id="withdrawMax"><?php echo $royaltys ?></span>
                                <span class="balance-int text-stress">元</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="ny-control-label ny-label  ny-label-darken">
                                提现中金额：
                            </div>
                            <div class="col-xs-8 ny-form-control">
                                <span class="text-stress" id="poundageContainer"><?php echo $royaltying ?></span>
                                <span class="text-stress"> 元</span>
                            </div>
                        </div>
                        <div class="form-group withdraw-account">
                            <div class="ny-control-label ny-label  ny-label-darken withdraw-amount">
                                收款微信：
                            </div>
                            <div class="col-xs-8 ny-form-control ny-input-group validate-control">
                                <div class="more-methods">
                                    <?php if ($weixin): ?>
                                        <?php echo $weixin ?><a href="javascript:" class="" id="changewx">修改收款微信</a>
                                    <?php else :?>
                                        <a href="javascript:" class="add-link" id="changewx">添加收款微信</a>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                        <div class="form-group withdraw-account">
                            <div class="ny-control-label ny-label  ny-label-darken">
                                预计到账日期：
                            </div>
                            <div class="col-xs-8 ny-form-control">
                                1-3个工作日到账，双休日和法定节假日顺延

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="ny-control-label ">
                                &nbsp;
                            </div>
                            <div class="col-xs-8 ny-form-control">
                                <a  href="javascript:" class="ny-btn btn-primary" id="refer" style="width: 132px;">申请提现</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="ny-panel-heading" style="margin-bottom:20px">
                <div class="ny-panel-title">
                    提现记录
                </div>
            </div>
            <div style="width: 90%;padding-top: 20px;">

                <!--以下为两个必须div元素-->
                <div id="table" style="text-align: center;"></div>
                 <div style="text-align: center; display: none;" id="nothing">暂无记录
                    <hr>
                </div>
                <div id="pageBar" ></div>
            </div>
            <div class="table-space" style="height: 35px;"></div>

            <div class="alert-warn-lg  clearfix" style="margin-top:100px">
                <!--<div class="ny-btn btn-primary-faker">发票申请说明：</div>-->
                <div class="tip-title">
                    提现须知：
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
        <div class="ny-panel" style="display: none;" id="weixin">
            <div class="ny-panel-heading">
                <div class="ny-panel-title">
                    提现账号
                </div>
                <a onclick="javascript:window.history.back(-1);" class="ny-back-btn"></a>
            </div>
            <div class="ny-panel-body">
                <div>
                    <form class="form-horizontal ny-form ny-tab-container tab-group-2" action="" id="modifyMobileForm">
<!--                        <div class="form-group">-->
<!--                            <div class="ny-control-label">-->
<!--                                会员ID：-->
<!--                            </div>-->
<!--                            <div class="col-xs-10 ny-form-control">-->
<!--                                124953-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="form-group hide-relate-1">
                            <div class="ny-control-label tab-relate-2">
                                <span class="necessary-mark">*</span> 微信提现ID：
                            </div>
                            <div class="col-xs-10 ny-form-control validate-control">
                                <input name="verifyCode" type="text" class="ny-input-reset tab-sub-input"  id="weixin_name" value=<?php echo $weixin ?>  >
                                <span class="text-stress margin-left-20">这里必须需要填写您的微信ID！不是手机号码，不是微信昵称！</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="ny-control-label">
                                &nbsp;
                            </div>
                            <div class="col-xs-10 ny-form-control">
                                <div class="hide">
                                    <input name="sign" value="modifyMobile" type="text" />
                                    <input name="url" value="/user/mobile/set.html" type="text" />
                                </div>
                                <a href="javascript:" class="ny-btn btn-primary" id="weixin_sure">确定</a>
                                <a href="javascript:" class="ny-btn " id="weixin_back" style=" margin-left:20px">返回</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="alert-warn-lg  clearfix" style="margin-top:200px">
                    <!--<div class="ny-btn btn-primary-faker">发票申请说明：</div>-->
                    <div class="tip-title">
                        提现账号：
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
    var nameList = ['ID', '提现申请人','金额','申请时间','提现状态','收款微信号', '审核员', '审核时间'] //table的列名
    var widthList = [100, 100, 100, 100, 100, 100, 100,100] //table每列的宽度

    $(function () {
        $.ajax({
            async: false,
            url: "index.php?r=admin%2Fapp%2Frefer",
            type: "post",
            dataType: "json",
            data: {
                code:1,
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

<script>
    $("#refer").click(function () {
        weixin_name = $("#weixin_name").val();
        if(weixin_name){
            $.post({
                url: "index.php?r=admin%2Fapp%2Frefer",
                type: "post",
                dataType: "json",
                data: {
                    _csrf: _csrf,
                },
                success: function (res) {

                    if(res.code == 0){
                        alert("提现金额不得少于1元");
                    }else if(res.code == 1){
                        alert("提现已提交，正在审核中");
                        $("#royaltying").val(res.royaltying);
                        $("#royaltys").val(0.00);
                        $.ajax({
                            async: false,
                            url: "index.php?r=admin%2Fapp%2Frefer",
                            type: "post",
                            dataType: "json",
                            data: {
                                code:1,
                                _csrf: _csrf,
                            },
                            success: function (res) {

                                json = res;
                                $(".layui-laypage-btn").trigger("click");
                            }
                        });
                    }else{
                        alert("未知错误");
                    }
                }
            });
        }else{
            $("#tixian").hide();
            $("#weixin").show();
        }

    });


</script>
<script>
    $("#changewx").click(function(){
        $("#tixian").hide();
        $("#weixin").show();
    });
    $("#weixin_back").click(function(){
        $("#tixian").show();
        $("#weixin").hide();
    });
    $("#weixin_sure").click(function () {
        weixin_name = $("#weixin_name").val();
        $.post({
            url: "index.php?r=admin%2Fapp%2Frefer",
            type: "post",
            dataType: "json",
            data: {
                _csrf: _csrf,
                weixin_name:weixin_name,
            },
            success: function (res) {
                if(res == 0){
                    alert("修改成功");
                   location.reload(); 
                }else{
                    alert("修改失败");
                }

            }
        });
    });
</script>
</html>