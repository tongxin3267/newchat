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
<form action="index.php" method="get">
    <div style="width: 100%;">
        <h4 style=" text-align: center;">提成审核表</h4>
        <br>
        <div style="margin-left: 5%;width: 20%;float: left;">
            搜索：
            <?php if ($search): ?>
                <input type="text" value="<?php echo $search ?>" style="border:1px solid #D9D9D9;" name="search" >
            <?php else :?>
                <input type="text" placeholder="请输入申请人用户名" style="border:1px solid #D9D9D9;" name="search">
            <?php endif; ?>
            <button style="margin-left: 2%;border: none;background-color: #55acee;color: white;"  type="submit" id="search">搜索</button>
        </div>
        <div style="width: 30%;float: left;">
                <span style="width:50%;">操作分类：</span>
             <select class="form-control" style="width:50%;margin-left: 15%;margin-top: -5%;" name="sure_status" id="select" onchange=func()>
                <?php if($sure_status == 4): ?>
                        <option value="4" selected>全部</option>
                        <option value="1" >未审核</option>
                        <option value="2" >已通过</option>
                        <option value="3" >已拒绝</option>
                <?php elseif($sure_status == 1) :?>
                <option value="4" >全部</option>
                        <option value="1" selected>未审核</option>
                        <option value="2" >已通过</option>
                        <option value="3" >已拒绝</option>
                <?php elseif($sure_status == 2) :?>
                <option value="4" >全部</option>
                        <option value="1" >未审核</option>
                        <option value="2" selected>已通过</option>
                        <option value="3" >已拒绝</option>
                <?php elseif($sure_status == 3) :?>
                        <option value="4" >全部</option>
                        <option value="1" >未审核</option>
                        <option value="2" >已通过</option>
                        <option value="3" selected>已拒绝</option>
                <?php endif; ?>
               </select>

        </div>

        <input type="hidden"  value="admin/app/sure" name="r">

        <br>
        <table class="hovertable" style="width: 90%;margin: 0 auto;text-align: center;margin-top: 2%;">

            <tr style="text-align: center;">
                <th style="text-align: center;">ID</th><th style="text-align: center;">用户ID</th><th style="text-align: center;">申请人</th><th style="text-align: center;">申请金额</th><th style="text-align: center;">收款微信号</th><th style="text-align: center;">申请时间</th><th style="text-align: center;">审核人ID</th><th style="text-align: center;">审核人</th><th style="text-align: center;">审核时间</th><th style="text-align: center;">操作</th>
            </tr>
           <?php if ($data): ?>
                <?php foreach ($data as $item) : ?>
                    <tr onmouseover="this.style.backgroundColor='#F2F2F2';" onmouseout="this.style.backgroundColor='WHITE';">
                        <td><?php echo $item['id']?></td><td><?php echo $item['user_id']?></td><td><?php echo $item['username']?></td>
                        <td><?php echo $item['total']?>元</td><td><?php echo $item['weixin']?></td><td><?php echo date('Y-m-d H:m:s', $item['time'])?></td>
                        <td><?php echo $item['sure_id']?></td><td><?php echo $item['sure_name']?></td><td><?php echo date('Y-m-d H:m:s', $item['sure_time'])?></td>
                        <?php if ($item['sure_status'] == 2): ?>
                            <td><button style="border: none;background-color: #55acee;color: white;" >已通过</button>
                        <?php elseif($item['sure_status'] == 3) :?>
                            <td><button style="border: none;background-color: red;color: white;">已拒绝</button>
                        <?php else :?>
                            <td><button style="border: none;background-color: #55acee;color: white;" class="button" id = <?php echo $item['id'] ?>>通过</button>&nbsp;&nbsp;&nbsp;<button style="border: none;background-color: red;color: white;" class="button" name = <?php echo $item['id'] ?>>拒绝</button></td>
                        <?php endif; ?>
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
    $(".button").click(function(e){
         var id =  e.target.id;
        var name = e.target.name;

        if(id){
            type = 1;
            code = id;
        }else if(name){
            type = 2;
            code = name;
        }
        $.ajax({
            url: "index.php?r=admin%2Fapp%2Fsure",
            type: "post",
            dataType: "json",
            async: false,
            data: {
                _csrf: _csrf,
                type:type,
                code:code,
            },
            success: function (res) {
               
                

            }
        });
    });
    function func(){
        $("#search").trigger("click");

    }

</script>