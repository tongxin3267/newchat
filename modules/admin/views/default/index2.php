<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by IntelliJ IDEA.
 * User: luwei
 * Date: 2017/10/3
 * Time: 11:44
 */
/* @var \yii\web\View $this */
/* @var \app\models\Admin[] $list */
$this->title = '我的账户';
$url_manager = Yii::$app->urlManager;
$urlManager = Yii::$app->urlManager;
$current_url = Yii::$app->request->absoluteUrl;
$return_url = $url_manager->createUrl(['admin/user/me']);
$this->params['active_nav_link'] = 'admin/user/me';

/** @var \app\models\Admin $model */
$model = Yii::$app->admin->identity;
?>

<div class="home-col">
        <div class="panel">
            <div class="panel-header">
                <div class="nav nav-left">
                    <span>商城信息</span>
                </div>
            </div>
            <div class="loading_3 toggle"></div>
            <div class="panel-body">
                <div class="row">
                	 <div class="col-3 text-center">
                        <div class="text-center numUrl">
                            <div style="font-size: 1.75rem;width: 100%"><?php echo $app_count;?></div>
                            <div>小程序总数</div>
                        </div>

                    </div>
                    <div class="col-3 text-center">
                        <div class="text-center numUrl">
                            <div style="font-size: 1.75rem;width: 100%"><?php echo $user_count;?></div>
                            <div>用户数</div>
                        </div>

                    </div>
                    <div class="col-3 text-center">
                        <div class="text-center numUrl">
                            <div style="font-size: 1.75rem;width: 100%"><?php echo $goods_count;?></div>
                            <div>商品数</div>
                        </div>
                    </div>
                    <div class="col-3 text-center">
                        <div class=" text-center numUrl">
                            <div style="font-size: 1.75rem;width: 100%">
                                <?php echo $order_count;?>
                            </div>
                            <div>订单数</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="panel panel-4">
            <div class="panel-header">
                <div class="nav nav-left">
                    <span>交易走势</span>
                </div>
                <ul class="nav nav-right order_statistics">

                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:" data="7">最近7天</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:" data="30">最近30天</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:" data="365">最近1年</a>
                    </li>
                </ul>
            </div>
            <div class="panel-body">
                <div id="echarts_1" style="height:18rem;" data="7"></div>
            </div>
        </div>

<script src="<?= Yii::$app->request->baseUrl ?>/statics/echarts/echarts.min.js"></script>
<script>
function qxt(){
	var days = $("#echarts_1").attr("data");
	$.ajax({
		url: '<?php echo  $urlManager->createUrl('admin/default/index');?>',
		type:'post',
        dataType: 'json',
        data: {
            days: days
        },
        success: function (res) {
            if (res.code != 0) {
                $.alert({
                    content: res.msg,
                });
                return;
            }
            setTimeout(function () {
                var echarts_1 = echarts.init(document.getElementById('echarts_1'));
                echarts1(res.data.datelist, res.data.applist, res.data.userlist,res.data.orderlist);
                window.onresize = function(){
                    echarts_1.resize();
                }
            }, 500);
        }
    });
}
$(function(){
    qxt();
});

	$(document).on('click', '.panel .panel-header .nav-link', function () {
        $(this).parents('.panel').find('.nav-link').removeClass('active');
        $(this).addClass('active');
		var val = $(this).attr('data');
		$("#echarts_1").attr("data",val);
		qxt();
    });
    var echarts1 = function(date, apps, users,orders){
        var echarts_1 = echarts.init(document.getElementById('echarts_1'));
        setTimeout(function () {
            var echarts_1_option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['小程序总数', '用户数', '成交订单数']
                },
                grid: {
                    left: '0%',
                    right: '0%',
                    bottom: '5%',
                    containLabel: true
                },
                xAxis: {
                    data: date,
                },
                yAxis: {
                    type: 'value'
                },
                series: [
                    {
                        name: '小程序总数',
                        type: 'line',
                        data: apps,
                    },
                    {
                        name: '用户数',
                        type: 'line',
                        data: users,
                    },
					{
                        name: '成交订单数',
                        type: 'line',
                        data: orders,
                    },
                ]
            };
            echarts_1.setOption(echarts_1_option);
        }, 500);
    };


</script>