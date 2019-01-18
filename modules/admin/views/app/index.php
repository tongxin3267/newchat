<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/10/3
 * Time: 12:04
 */
/* @var \yii\web\View $this */
$this->title = '我的小程序商城';
$url_manager = Yii::$app->urlManager;
$current_url = Yii::$app->request->absoluteUrl;
$this->params['active_nav_link'] = 'admin/app/index';
?>
<div class="mb-3">
    <a href="javascript:" class="btn btn-sm btn-primary mr-3 add-app">添加小程序商城</a>
  
</div>
<table class="table bg-white">
    <thead>
    <tr>
        <th>ID</th>
        <th>名称</th>
      <th>到期时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <?php if (count($list) == 0) : ?>
        <tr>
            <td colspan="3" class="text-center p-5">
                <a href="javascript:" class="add-app">添加小程序商城</a>
            </td>
        </tr>
    <?php endif; ?>
    <?php foreach ($list as $item) : ?>
        <tr>
            <td><?= $item->id ?></td>
            <td>
                <a href="<?= $url_manager->createUrl(['admin/app/entry', 'id' => $item->id]) ?>"><?= $item->name ?></a>
            </td>
           <td>
                <?php  echo $expire_time ?>

            </td>
            <td>
                <a href="<?= $url_manager->createUrl(['admin/app/entry', 'id' => $item->id]) ?>">进入商城</a>
           
            </td>
        </tr>
    <?php endforeach; ?>
</table>
    <?php if ($res) : ?>
      <div style="">
          <input type="text" style="color: red;border: none;margin-left: 30%;width:600px;text-align:center;" value="<?php echo $res?>" disabled>
      </div>
  <?php endif; ?>

<?= $this->render('removal-modal'); ?>
<script>

 $(document).on("click", ".add-app", function () {
        $.post({
            url: "index.php?r=admin%2Faccredit%2Findex",
            type: "post",
            dataType: "json",
            data: [],
            complete:function(res){
                console.log(res.responseJSON.code);
                if(res.responseJSON.code == 1){
                    // console.log(res.responseJSON.url);
                    window.location.href = res.responseJSON.url;
                }else if(res.responseJSON.code == 0){
                    alert("参数错误");
                }else if(res.responseJSON.code == 2){
                    alert("每个账号只能有一个小程序");
                }
            }

        });
    });
    $(document).on("click", ".recycle-btn", function () {
        var href = $(this).attr("href");
        $.myConfirm({
            content: "确认将小程序放进回收站？可以从回收站恢复。",
            confirm: function () {
                $.myLoading({
                    title: "正在提交",
                });
                $.ajax({
                    url: href,
                    dataType: "json",
                    success: function (res) {
                        $.myLoadingHide();
                        $.myToast({
                            content: res.msg,
                            callback: function () {
                                location.reload();
                            }
                        });
                    }
                });

            }
        });
        return false;
    });

    $(document).on("click", ".disabled-btn", function () {
        var href = $(this).attr("href");
        var aText = $(this).text();
        console.log(aText);
        $.myConfirm({
            content: "确认将小程序" + aText + "?",
            confirm: function () {
                $.myLoading({
                    title: "正在提交",
                });
                $.ajax({
                    url: href,
                    dataType: "json",
                    success: function (res) {
                        $.myLoadingHide();
                        $.myToast({
                            content: res.msg,
                            callback: function () {
                                location.reload();
                            }
                        });
                    }
                });

            }
        });
        return false;
    });
</script>