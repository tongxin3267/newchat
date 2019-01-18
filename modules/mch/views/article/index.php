<?php
defined('YII_ENV') or exit('Access Denied');
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/6/19
 * Time: 16:52
 */
//$cat = [
  //  1 => '关于我们',
  //  2 => '服务中心',
//];
$urlManager = Yii::$app->urlManager;
$this->title = $cat[$cat_id];
//$this->params['page_navs'] = [
  //  [
  //      'name' => '关于我们',
  //      'active' => $cat_id == 1,
  //      'url' => $urlManager->createUrl(['mch/article/index', 'cat_id' => 1,]),
  //  ],
  //  [
  //      'name' => '服务中心',
  //      'active' => $cat_id == 2,
  //      'url' => $urlManager->createUrl(['mch/article/index', 'cat_id' => 2,]),
  //  ],
//];
?>
<style>
    .cr{
        display: inline-block;
        width: 100px;
        height: 30px;
        text-align: center;
    }
    .cr>a{
        text-decoration: none;
        outline: none;
        color: #000;
        font-size: 16px;
        line-height: 30px;
    }
    .cr:hover{
        background-color: #eee;
    }
</style>
<div class="panel mb-3">
    <div class="panel-header">
        <span><?= $this->title ?></span>
      	<span class="cr" <?php if ($cat_id == 1) : ?> style="background-color: #eee;" <?php endif;?>>
            <a href="index.php?r=mch%2Farticle%2Findex&cat_id=1">关于我们</a>
        </span>
        <span class="cr" <?php if ($cat_id != 1) : ?> style="background-color: #eee;" <?php endif;?>>
            <a href="index.php?r=mch%2Farticle%2Findex&cat_id=2">服务中心</a>
        </span>
        <?php if ($cat_id != 1) : ?>
            <ul class="nav nav-right">
                <li class="nav-item">
                    <a class="nav-link"
                       href="<?= $urlManager->createUrl(['mch/article/edit', 'cat_id' => 2]) ?>">添加文章</a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
    <div class="panel-body">
        <table class="table table-bordered bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th>类别</th>
                <th>标题</th>
                <th>排序</th>
                <th>操作</th>
            </tr>
            </thead>
            <?php foreach ($list as $item) : ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td><?= $cat[$item->article_cat_id] ?></td>
                    <td><?= $item->title ?></td>
                    <td><?= $item->sort ?></td>
                    <td>
                        <a class="btn btn-sm btn-primary"
                           href="<?= $urlManager->createUrl(['mch/article/edit', 'cat_id' => $item->article_cat_id, 'id' => $item->id]) ?>">编辑</a>
                        <?php if ($cat_id != 1) : ?>
                            <a class="btn btn-sm btn-primary copy"
                               data-clipboard-text="/pages/article-detail/article-detail?id=<?= $item->id ?>"
                               href="javascript:" hidden>复制链接</a>
                            <a class="btn btn-sm btn-danger article-delete"
                               href="<?= $urlManager->createUrl(['mch/article/delete', 'id' => $item->id]) ?>">删除</a>
                        <?php else : ?>
                            <a class="btn btn-sm btn-primary copy"
                               data-clipboard-text="/pages/article-detail/article-detail?id=about_us"
                               href="javascript:" hidden>复制链接</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<script>
    $(document).on("click", ".article-delete", function () {
        var href = $(this).attr("href");
        $.confirm({
            content: "确认删除？",
            confirm: function () {
                $.loading();
                $.ajax({
                    url: href,
                    dataType: "json",
                    success: function (res) {
                        location.reload();
                    }
                });
            }
        });
        return false;
    });
</script>