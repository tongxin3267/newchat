<!DOCTYPE html>
<html>
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">

<meta name="baidu-site-verification" content="uOnsbIjplu">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=9">




<link media="all" href="<?= Yii::$app->request->baseUrl ?>/statics/admin/css/login.css" type="text/css" rel="stylesheet">
</head>
<body class="index" >


<div class="sitebar">
    <div class="wrap">
    <a href="/"> 
    <span class="selected">返回首页</span>
      </a> 
  </div>
</div>
<div class="header">
    <div class="wrap clr">
        <h1 class="logo">微信支付商户平台</h1>
        
  </div>
</div>



<div class="mask-layer hide" id="header-masker"></div>
<div class="banner">
    <div class="slider">
    	<div class="cms-banner cms-area" id="cmspic_1000" home="true"><ul><li style="background-image: url('<?= Yii::$app->request->baseUrl ?>/statics/admin/images/0'); opacity: 1; z-index: 1;">
        
        </li></ul></div>
    </div>
    <div class="wrap clr">
                <div class="login">
            <div class="login-weixin" id="IDWechatLogin">
            	<h2 class="title">微信扫码，登录账号</h2>
                <div class="scan-qrcode">
                    <div class="qrcode-img" id="IDQrcode">
                        <div ><canvas width="180" height="180" style="display: none;"></canvas><img style="display: block;" src="" id = "erweima"></div>
                        
                        
                        
     
 
     
    

   
    
     <div class="div2" id="div2" style="display:none">
     
     
    
    
                          <div class="alt" id="IDQrcodeInvalid">
                            <a  href="javascript:void(0)" id ="login" > <div class="void">
                                 <i class="ico ico-refresh"></i>
                              <p class="txt">二维码失效，点击刷新</p>
                            </div></a>
                            <div class="mask"></div>
                            
                            </div>
                        </div>
                    </div>
                    
                    
        <script type="text/javascript">
        //获取div
        var div2 = document.getElementById("div2");
       

        //计时
        var countTime = 300;
        function controlTime()
        {
          setTimeout(controlTime,1000);
          --countTime;
          if(countTime < 1)
          {
            div2.style.display = "block";
           
            if(countTime < 0)
            {
              div2.style.display = "block";
             
             
            }
          }
        }
        controlTime();
    </script>
    
    
    
    <script type="text/javascript">
// 0秒模拟点击
setTimeout(function() {
// IE
if(document.all) {
document.getElementById("login").click();
}
// 其它浏览器
else {
var e = document.createEvent("MouseEvents");
e.initEvent("click", true, true);
document.getElementById("login").dispatchEvent(e);
}
}, 0);
</script>

                    <div class="page-msg icon-center hide" id="IDQrcodeScaned">
                        <div class="inner">
                            <div class="msg-ico"><i class="ico-msg succ"></i></div>
                            <div class="msg-cnt">
                                <h4>扫码成功，</h4>
                                <p>请等待进入系统</p>
                            </div>
                        </div>
                    </div>
                   
                   
    
                    
                    <div class="tips-error hide" id="IDQrcodeError"></div>
                </div>
            </div>
           

           
            <div class="login-service"  style="margin-top:10px">
                <a href="" target="_top">扫码关注公众号即可登录</a>
               </br>
                <div class="tool_area">
<label for="js_agree" class="frm_control_checkbox frm_checkbox_label">
<i class="icon_checkbox"></i>
<input class="frm_checkbox" id="js_agree" name="agree" value="" type="checkbox">
我同意并遵守<a href="/web/index.php?r=admin%2Fpassport%2Fagreement"  target="_blank">《服务协议》</a></label>
</div>
              
               
            </div>
			
        </div>
                <input id="seed" name="time_seed" value="31353432393537393039" type="hidden">
    </div>
    <div class="notice">
        <div class="wrap">
            <div class="cms-notice cms-area clr" id="cmsanm_6000" home="true" link-list-id="6200"><h2>最新公告</h2><ul><li><a target="_top"  title="增加淘宝、天猫、1688商品快速采集功能"><span class="time">[10.31]</span><span class="name">增加淘宝、天猫、1688商品快速采集功能</span></a></li><li><a target="_top"  title="更新拼团、秒杀、预约、刮刮卡、积分商城"><span class="time">[10.25]</span><span class="name">增加更新拼团、秒杀、预约、刮刮卡、积分商城</span></a></li><li><a target="_top" title="增加供货商入驻、分销功能"><span class="time">[08.21]</span><span class="name">增加供货商入驻、分销功能</span></a></li></ul><p class="more"><a   href="/help/public/index.php"  target="_blank">更多功能<i class="arr">&gt;&gt;</i></a></p></div>
        </div>
        <div class="mask"></div>
    </div>
</div>
<!-- 登录 ]] -->
<!-- 平台能力 [[ -->
<div class="platform">
    <div class="wrap">
        <h2 class="big-title underline">5步轻松发布小程序</h2>
        <ul class="clr">
            <li>
                <a class="ability" href="/help/public/index.php"  target="_blank" onClick="pgvSendClick({hottag:'PAY.INDEX.ABILITY.PAYMENT_PRODUCT'});">
                   
                    <h3 class="tit underline">① 微信公众号平台</h3>
                    <p class="txt">注册并认证公众号</p>
                </a>
            </li>
            <li>
                <a class="operate" href="/help/public/index.php"  target="_blank" onClick="pgvSendClick({hottag:'PAY.INDEX.ABILITY.BUSINESS_TOOL'});">
                   
                    <h3 class="tit underline">② 微信公众号平台</h3>
                    <p class="txt">申请微信支付</p>
                </a>
            </li>
            
             <li>
                <a class="operate" href="/help/public/index.php"  target="_blank" onClick="pgvSendClick({hottag:'PAY.INDEX.ABILITY.BUSINESS_TOOL'});">
                  
                    <h3 class="tit underline">③ 微帮小程序平台</h3>
                    <p class="txt">注册小程序账号</p>
                </a>
            </li>
            
            
            <li>
                <a class="capital" href="/help/public/index.php"  target="_blank"  onClick="pgvSendClick({hottag:'PAY.INDEX.ABILITY.FUND_MANAGE'});">
                   
                    <h3 class="tit underline">④ 微帮小程序平台</h3>
                    <p class="txt">填写各项，提交审核</p>
                </a>
            </li>
            <li class="last">
                <a class="safety" href="/help/public/index.php"  target="_blank"  onClick="pgvSendClick({hottag:'PAY.INDEX.ABILITY.EXPAND_TOOL'});">
                   
                    <h3 class="tit underline">⑤ 微帮小程序平台</h3>
                    <p class="txt">通过审核</p>
                </a>
            </li>
            
            
  </ul>
        <p class="more"><a href="/help/public/index.php"  target="_blank"  onClick="pgvSendClick({hottag:'PAY.INDEX.ABILITY.MORE'});">查看更多<i class="arr">&gt;&gt;</i></a></p>
    </div>
   <input type="text" hidden value="<?php echo $sale_id?>" id="sale_id">
</div>






<script>

        $(document).on('click', '#login', function () {
            var sale_id = $("#sale_id").val();
        
            $.post({
                url: "index.php?r=admin%2Fpassport%2Fcode",
                type: "post",
                dataType: "json",
                data: {
                    'sale_id':sale_id,
                },
                success: function (res) {

                    console.log(res.scene_id);
                    $("#erweima").attr('src',res.qrcode);
                    check_login(res.scene_id);
                }
            })
        });


    function check_login($login) {

        var scene_id = $login;
        $.post({
            url: "index.php?r=admin%2Fpassport%2Fcheck",
            type: "post",
            dataType: "json",
            data: {
                'scene_id': scene_id
            },
            success: function (res) {

                if (res.code == 0) {
                    setTimeout("check_login("+$login+")", 2000);
                }else if(res.code == 1){
                    var username = res.username;
                    var password = "123";
                    $.ajax({
                        url:'<?=Yii::$app->urlManager->createUrl('admin/passport/login')?>',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'username': username,
                            'password': password,
                            _csrf: _csrf,
                        },
                        success:function (res) {
								console.log(res);
                            if (res.code === 1) {
                                $.myAlert({
                                    content: res.msg
                                });
                            }else if (res.code === 7){
                                var id = res.app_id;
                               var str ="<?= \Yii::$app->urlManager->createUrl(['admin/app/entry']) ?>" + '&id=' + id;
                           
                            location.href = str;
                            }else  {
                                location.href = "<?= \Yii::$app->urlManager->createUrl('admin/user/me') ?>";
                            }
                        }
                    })
                }


            }
        });


    }
</script>






















</body>
</html>
