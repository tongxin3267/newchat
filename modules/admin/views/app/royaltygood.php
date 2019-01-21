<?php

use yii\widgets\LinkPager;

?>
<style type="text/css">
    table.hovertable {
        font-family: verdana,arial,sans-serif;
        font-size:11px;
        color:#333333;
        border-width: 1px;
        border-color: #999999;
        border-collapse: collapse;
    }
    table.hovertable th {
        background-color:#f2f2f2;
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #e6e6e6;
    }
    table.hovertable tr {
        background-color:white;
    }
    table.hovertable td {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #e6e6e6;
    }
</style>
<form action="<?= Yii::$app->urlManager->createUrl(['admin/app/royaltygood']) ?>" method="get">
    <div style="width: 100%;">
        <h4 style=" text-align: center;">商品提成</h4>
        <br>
        <div style="margin-left: 5%;width: 22%;float: left;">
            搜索：
            <?php if ($search): ?>
                <input type="text" value="<?php echo $search ?>" style="border:1px solid #D9D9D9;" name="search" >
            <?php else :?>
                <input type="text" placeholder="请输入订单号 查询" style="border:1px solid #D9D9D9;" name="search">
            <?php endif; ?>
            <button style="margin-left: 2%;border: none;background-color: #55acee;color: white;"  type="submit" id="search">搜索</button>
        </div>
        <div style="width: 30%;float: left;">
            <span style="width:50%;">订单分类：</span>
             <select class="form-control" style="width:50%;margin-left: 15%;margin-top: -5%;" name="order_classification" id="select" onchange=func()>
                <?php if($order_classification == 1) :?>
                    <option value="1" selected>普通订单</option>
                    <option value="2" >秒杀订单</option>
                    <option value="3" >拼团订单</option>
                <?php elseif($order_classification == 2) :?>
                    <option value="1" >普通订单</option>
                    <option value="2" selected>秒杀订单</option>
                    <option value="3" >拼团订单</option>
                <?php elseif($order_classification == 3) :?>
                    <option value="1" >普通订单</option>
                    <option value="2" >秒杀订单</option>
                    <option value="3" selected>拼团订单</option>
                <?php endif; ?>
            </select>

        </div>
        <div style="width: 40%;float: left;">
            <span style="width:50%;">查询日期：</span>
             <input type="date" style="border:1px solid #D9D9D9;" name="query_start_date" value="<?php echo date('Y-m-d',$query_start_date)?>">——
            <?php if($query_end_date) :?>
                <input type="date" style="border:1px solid #D9D9D9;" name="query_end_date" value="<?php echo date('Y-m-d',$query_end_date)?>">
            <?php else:?>
                <input type="date" style="border:1px solid #D9D9D9;" name="query_end_date" value="<?php echo date('Y-m-d',time())?>">
            <?php endif;?>

            </select>

        </div>
        <input type="hidden"  value="admin/app/royaltygood" name="r">

        <br>
        <table class="hovertable" style="width: 90%;margin: 0 auto;text-align: center;margin-top: 2%;">

            <tr style="text-align: center;">
                <th style="text-align: center;">订单号</th><th style="text-align: center;">购买用户</th><th style="text-align: center;">订单金额</th><th style="text-align: center;">订单提成</th><th style="text-align: center;">订单生成时间</th><th style="text-align: center;">操作</th>
            </tr>
            <?php if ($orders): ?>

                <?php foreach ($orders as $k=>$item) : ?>

                    <tr onmouseover="this.style.backgroundColor='#F2F2F2';" onmouseout="this.style.backgroundColor='WHITE';">
                      <td><?php echo $item['order_no']?></td>
                        <td><?php echo $item['name']?></td><td><?php echo $item['pay_price']?></td><td><?php echo $item['royalty']?></td><td><?php echo date('Y-m-d H:m:s', $item['addtime'])?></td><td></td>

                    </tr>

                <?php endforeach; ?>
            <?php else :?>
                <tr><td colspan="10">暂无记录</td></tr>
            <?php endif; ?>
        </table>
        <div style="width: 90%;margin-left: 5%;margin-top: 2%;text-align: center;">
            <div style="display: inline-block;" >
                <?= LinkPager::widget(['pagination' => $pages, 'nextPageLabel' => '下一页','prevPageLabel' => '上一页', 'firstPageLabel' => '首页',
                    'lastPageLabel' => '尾页', ]); ?>
            </div>
        </div>
    </div>
</form>

<script>

    function func(){
        $("#search").trigger("click");

    }

</script>