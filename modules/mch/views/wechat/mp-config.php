<?php
use yii\widgets\ActiveForm;
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/12/28
 * Time: 15:53
 */
$this->title = '微信配置';

?>

<link href="<?= Yii::$app->request->baseUrl ?>/statics/mch/css/config.css" rel="stylesheet">

<style type="text/css">
    .demo{ background: #ffded7; padding: 2em 0;}
    a:hover,a:focus{
        outline: none;
        text-decoration: none;
    }
    .tab .nav-tabs{
        padding-left: 15px;
        border-bottom: 4px solid #692f6c;
    }
    .tab .nav-tabs li a{
        color: #fff;
        padding: 10px 20px;
        margin-right: 10px;
        background: #692f6c;
        text-shadow: 1px 1px 2px #000;
        border: none;
        border-radius: 0;
        opacity: 0.5;
        position: relative;
        transition: all 0.3s ease 0s;
    }
    .tab .nav-tabs li a:hover{
        background: #692f6c;
        opacity: 0.8;
    }
    .tab .nav-tabs li.active a{
        opacity: 1;
    }
    .tab .nav-tabs li.active a,
    .tab .nav-tabs li.active a:hover,
    .tab .nav-tabs li.active a:focus{
        color: #fff;
        background: #692f6c;
        border: none;
        border-radius: 0;
    }
    .tab .nav-tabs li a:before,
    .tab .nav-tabs li a:after{
        content: "";
        border-top: 42px solid transparent;
        position: absolute;
        top: -2px;
    }
    .tab .nav-tabs li a:before{
        border-right: 15px solid #692f6c;
        left: -15px;
    }
    .tab .nav-tabs li a:after{
        border-left: 15px solid #692f6c;
        right: -15px;
    }
    .tab .nav-tabs li a i,
    .tab .nav-tabs li.active a i{
        display: inline-block;
        padding-right: 5px;
        font-size: 15px;
        text-shadow: none;
    }
    .tab .nav-tabs li a span{
        display: inline-block;
        font-size: 14px;
        letter-spacing: -9px;
        opacity: 0;
        transition: all 0.3s ease 0s;
    }
    .tab .nav-tabs li a:hover span,
    .tab .nav-tabs li.active a span{
        letter-spacing: 1px;
        opacity: 1;
        transition: all 0.3s ease 0s;
    }
    .tab .tab-content{
        padding: 30px;
        background: #fff;
        font-size: 16px;
        color: #6c6c6c;
        line-height: 25px;
    }
    .tab .tab-content h3{
        font-size: 24px;
        margin-top: 0;
    }
    @media only screen and (max-width: 479px){
        .tab .nav-tabs li{
            width: 100%;
            margin-bottom: 5px;
            text-align: center;
        }
        .tab .nav-tabs li a span{
            letter-spacing: 1px;
            opacity: 1;
        }
    }
    /*提交审核等待*/
    #btn{
        height: 30px;
        width: 100px;
        display: inline-block;
        margin-left: 80%;
        position: relative;
    }
    .she{
        height: 30px;
        width: 100px;
        border: none;
        border-radius: 3px;
        background-color: #0275d8;
        color: white;
    }
    
    #btn_img{
        height: 30px;
        width: 100px;
        display: inline-block;
    }
    #load{
        position: absolute;
        top: 20%;
        left: 40%;
        width: 20px;
        display: none;
        /*background-color: red;*/
    }
</style>

<div class="step-bar-outer clearfix text-center margin-bottom-30" style="margin-left: -150px;" >
    <div class="step-bar step-bar-begin step--current" >
        <div class="step-colored">
							<span>
								① 微信配置
							</span>
        </div>
        <span class="step-arrow-right" ></span>

    </div>

    <div class="step-bar step-bar-center">
        <span class="step-arrow-left left" ></span>
        <div class="step-colored" id="step_two">
							<span>
								② 提交审核小程序
							</span>
        </div>
        <span class="step-arrow-right"></span>
    </div>
    <div class="step-bar step-bar-end">
        <span class="step-arrow-left right"></span>
        <div class="step-colored" id="step_three">
							<span>
								③ 发布小程序
							</span>
        </div>
    </div>
</div>
<div class="contents clearfix" style="margin-left: -150px;">
    <div class="content flex-row one" style="display: block;">
        <form class="auto-form" method="post">
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">小程序AppId</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" value="<?= $model->app_id ?>" name="app_id">
                </div>
              
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">小程序AppSecret</label>
                </div>
                <div class="col-sm-6">
                    <div class="input-hide">
                        <input class="form-control" value="<?= $model->app_secret ?>" name="app_secret">
                       
                    </div>
                  
                </div>
              <a target="_blank" href="<?= Yii::$app->urlManager->createUrl(['admin/passport/appsecret']) ?>"><img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/config/wenhao.png" style="width: 24px;height:24px;"></a>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">微信支付商户号</label>
                </div>
                <div class="col-sm-6">
                    <input class="form-control" value="<?= $model->mch_id ?>" name="mch_id">
                </div>
              <a target="_blank" href="<?= Yii::$app->urlManager->createUrl(['admin/passport/wxshh']) ?>"><img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/config/wenhao.png" style="width: 24px;height:24px;"></a>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label required">微信支付Api密钥</label>
                </div>
                <div class="col-sm-6">
                    <div class="input-hide">
                        <input class="form-control" value="<?= $model->key ?>" name="key" >
                       
                    </div>
                </div>
              <a target="_blank" href="<?= Yii::$app->urlManager->createUrl(['admin/passport/wxapi']) ?>"><img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/config/wenhao.png" style="width: 24px;height:24px;"></a>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">微信支付证书</label>
                </div>
                <div class="col-sm-6">
                    <div class="input-hide">
                        <input type="file" id="upload" name="UploadForm[imageFile]" /><span style="color: red;font-size: 10px;">注意：确认商户号已授权给小程序！</span>
                    </div>
                </div>
              <a target="_blank" href="<?= Yii::$app->urlManager->createUrl(['admin/passport/wxzs']) ?>"><img src="<?= Yii::$app->request->baseUrl ?>/statics/mch/images/config/wenhao.png" style="width: 24px;height:24px;"></a>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">微信支付apiclient_cert.pem</label>
                </div>
                <div class="col-sm-6">
                    <div class="input-hide">
                        <textarea rows="5" class="form-control secret-content" id = "cert_pem" name="cert_pem"><?= $model->cert_pem ?></textarea>
                        <!--                        <div class="tip-block">已隐藏内容，点击查看或编辑</div>-->
                    </div>
                    <!--                    <div class="fs-sm text-muted">使用文本编辑器打开apiclient_cert.pem文件，将文件的全部内容复制进来</div>-->
                </div>
            </div>
            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                    <label class="col-form-label">微信支付apiclient_key.pem</label>
                </div>
                <div class="col-sm-6">
                    <div class="input-hide">
                        <textarea rows="5" class="form-control secret-content" id = "key_pem" name="key_pem"><?= $model->key_pem ?></textarea>
                        <!--                        <div class="tip-block">已隐藏内容，点击查看或编辑</div>-->
                    </div>
                    <!--                    <div class="fs-sm text-muted">使用文本编辑器打开apiclient_key.pem文件，将文件的全部内容复制进来</div>-->
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group-label col-sm-2 text-right">
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary auto-form-btn" href="javascript:">保存</a>
                    <a class="btn btn-primary" href="javascript:" id = "step_1" style="float: right;">下一步</a>
                </div>
            </div>
        </form>
    </div>
    <div class="content flex-row two" style="display: none;">

        <div style="margin-top: 10px;">
            <span >审核版本</span>
            <?php if ($ret['status'] != 2) : ?>
                <div id="btn" style="">
                    <button id="shenhe" class="she" style="margin-left: 45%;margin-top: 5%">提交微信审核</button>
                    <div id="btn_img">
                        <img src="<?= Yii::$app->request->baseUrl ?>/statics/images/load.png" id="load">
                    </div>
                </div>
          		<div style="float: right;margin:-1.2% 5% 0 0">如果您已经授权给其他第三方请先取消授权</div>
            <?php endif; ?>
            <hr>

            <div>
                <?php if ($ret['status'] == 4) : ?>
                    <span>暂无审核版本</span>
                <?php endif; ?>
                <?php if ($ret['status'] == 5) : ?>
                    <span><span>版本<?php echo $ret['auditid']?></span>发布成功</span>
                <?php endif; ?>
                <?php if ($ret['status'] == 0) : ?>
                    <span><span>版本<?php echo $ret['auditid']?></span>审核成功，请发布小程序</span>
                <?php endif; ?>
                <?php if ($ret['status'] == 1) : ?>
                    <span><span>版本<?php echo $ret['auditid']?></span>审核失败</span>
                    <span><?php echo $ret['reason'] ?></span>
                <?php endif; ?>
                <?php if ($ret['status'] == 2) : ?>
                    <span><span>版本<?php echo $ret['auditid']?></span>审核中</span>
                <?php endif; ?>
            </div>

        </div>
    <div></div>
        <a class="btn btn-primary" href="javascript:" id = "step_2" style="margin-left:45%;margin-top:5%; ">上一步</a>
        <a class="btn btn-primary" href="javascript:" id = "step_3" style="margin-left:3%;margin-top:5%;">下一步</a>
    </div>
    <div class="content flex-row three" style="display: none;">
        <button style="height: 50px;width: 150px;border: none;border-radius: 3px;margin-left: 40%;background-color: #0275d8;color: white;font-size:25px;" id="fabu">发布小程序</button>
        <a class="btn btn-primary" href="javascript:" id = "step_4" style="margin-left: 10%;margin-top:1%;">上一步</a>

    </div>
</div>

<script src="<?= Yii::$app->request->baseUrl ?>/statics/js/ajaxfileupload.js"></script>
<script type="text/javascript" src="<?= Yii::$app->request->baseUrl ?>/statics/mch/js/jquery.SuperSlide.2.1.1.js"></script>

<script>

        $("input#upload").change(function () {
            var file = document.getElementById("upload").files[0];

            if(file.type != "application/x-zip-compressed"){
                alert("请上传正确格式的压缩包");
            }else if (file.size > 20000) {
                alert("文件大小不能超过20KB");
            }else {
                $.ajaxFileUpload({
                    url: "index.php?r=mch%2Fwechat%2Fupload",
                    secureuri: false,
                    data:{"id":"id"},
                    fileElementId:'upload',
                    dataType: 'json',
                    complete:function (res) {
                  
                        var jsonObj = $.parseJSON(res.responseText.replace(/<.*?>/ig,""));
                        console.log(jsonObj.code);
                        if(jsonObj.code == 0){
                            alert("上传成功");
                            $("#cert_pem").text(jsonObj.cert_pem);
                            $("#key_pem").text(jsonObj.key_pem);
                        
                        }else if(jsonObj.code == 1){
                            alert("上传失败");
                        }
                    }
                });
            }
        });

</script>
<script type="text/javascript">
    jQuery(".change-main").slide({ titCell:".flex-row li", mainCell:".contents",delayTime:0 });
</script>
<script>
    $("#shenhe").click(function () {
        $('#shenhe').css('display','none');
            $('#load').css('display','block');
            $.ajax({
                url: "index.php?r=mch%2Fapply%2Findex",
                type: "post",
                dataType: "json",
                data: [],
                complete:function (res) {
                  console.log(res);
                    if(res.responseText == 0 || res.responseJSON == 0){
                           $('#shenhe').css('display','block');
                        $('#load').css('display','none');
                        alert("提交审核成功");
                       window.location.reload();
                    }else{
                         $('#shenhe').css('display','block');
                        $('#load').css('display','none');
                        alert(res.responseJSON.errmsg);
                    }
                }
            });
    });
</script>

<script>
    $("#fabu").click(function () {
    
        $.ajax({
            url: "index.php?r=mch%2Fapply%2Frelease",
            type: "post",
            dataType: "json",
            data: [],
            complete:function (res) {
              console.log(res);
                if(res.responseJSON.errcode == 0){
                    alert("发布小程序成功");
                      window.location.reload();
                }else if(res.responseJSON.errcode == 85052){
                         alert("您提交的小程序已经发布成功，请勿重复操作");
                  }else{
                    alert(res.responseJSON.errmsg);
                }
            }
        });
    });
</script>
<script>
    $("#preview").click(function () {
        $.ajax({
            url: "index.php?r=mch%2Fapply%2Fversion",
            type: "post",
            dataType: "json",
            data: [],
            complete:function (res) {
                console.log(res);
            }
        });
    });
</script>
<script>
    $("#step_1").click(function () {
        $(".step--current .step-colored").css("background-color","#F2F2F2");
        $(".step--current .step-colored").css("color","black");
        $(".step--current .step-arrow-right").css("border-left-color","#F2F2F2");

        $("#step_two").css("background-color","#00AAFF");
        $("#step_two").css("color","white");
        $(".step-bar-center .step-arrow-right").css("border-left-color","#00AAFF");
        $(".step--current ~ .step-bar .left").css("border-color","#00AAFF #00AAFF #00AAFF transparent");
        $(".one").css("display","none");
        $(".two").css("display","block")
    });
    $("#step_2").click(function () {
        $(".step--current .step-colored").css("background-color","#00AAFF");
        $(".step--current .step-colored").css("color","white");
        $(".step--current .step-arrow-right").css("border-left-color","#00AAFF");
        $(".step--current ~ .step-bar .left").css("border-color","#F2F2F2 #F2F2F2 #F2F2F2 transparent");
        $("#step_two").css("background-color","#F2F2F2");
        $("#step_two").css("color","black");
        $(".step-bar-center .step-arrow-right").css("border-left-color","#F2F2F2");
        $(".one").css("display","block");
        $(".two").css("display","none")
    });
    $("#step_3").click(function () {
        $("#step_three").css("background-color","#00AAFF");
        $("#step_three").css("color","white");
        $(".step-bar-end .step-arrow-right").css("border-left-color","#00AAFF");
        $(".step--current ~ .step-bar .left").css("border-color","#F2F2F2 #F2F2F2 #F2F2F2 transparent");
        $(".step--current ~ .step-bar .right").css("border-color","#00AAFF #00AAFF #00AAFF transparent");
        $("#step_two").css("background-color","#F2F2F2");
        $("#step_two").css("color","black");
        $(".step-bar-center .step-arrow-right").css("border-left-color","#F2F2F2");
        $(".three").css("display","block");
        $(".two").css("display","none")
    });
    $("#step_4").click(function () {
        $("#step_three").css("background-color","#F2F2F2");
        $("#step_three").css("color","black");
        $(".step-bar-end .step-arrow-right").css("border-left-color","#F2F2F2");
        $(".step--current ~ .step-bar .right").css("border-color","#F2F2F2 #F2F2F2 #F2F2F2 transparent");
        $(".step--current ~ .step-bar .left").css("border-color","#00AAFF #00AAFF #00AAFF transparent");
        $("#step_two").css("background-color","#00AAFF");
        $("#step_two").css("color","white");
        $(".step-bar-center .step-arrow-right").css("border-left-color","#00AAFF");
        $(".two").css("display","block");
        $(".three").css("display","none")
    });
</script>


