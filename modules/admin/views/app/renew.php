
<link href="<?= Yii::$app->request->baseUrl ?>/statics/renew/css/paypage.css" rel="stylesheet">

<div class="ny-main">
    <div id="contentBox" >
        <div class="ny-panel" style="padding: 0px;">
            <div class="ny-panel-heading">
                <div class="ny-panel-title">
                    充值中心
                </div>
            </div>
            <div class="ny-panel-body">
                <div class="ny-tabs-container ny-tabs-container-new clearfix">
                    <ul class="clearfix">
                        <li class="ny-tab ny-tab--current"> <a href="#a_null">在线充值</a> </li>
                    </ul>
                </div>
                <div class="payments-block margin-bottom-20">
                    <form id="paymentForm" action="" method="post" target="_blank" class="form-horizontal ny-form">
                        <input name="_csrf" value="" type="hidden" />
                        <div class="form-group payment-amount">
                            <div class="ny-control-label ny-label ny-label-darken">
                                充值金额：
                            </div>
                            <div class="col-xs-8 ny-form-control ny-input-group validate-control">
                                <span class="balance-int text-stress"></span>
                                <span class="balance-decimal text-stress balance-int" id="payAmount"><?php echo $data['payAmount'] ?> 元</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="ny-control-label ny-label ny-label-darken payment-type">
                                支付方式：
                            </div>
                            <div class="col-xs-10 ny-form-control">
                                <div class="payment-tier clearfix">
                                    <!-- 微信支付 -->
                                    <div class="payment-item">
                                        <input id="weixin" name="payType" value="weixin" checked="checked" type="radio" />
                                        <label for="weixin" class="pay-select"> <span class="payment-choose1 payment-weixin"></span> <i></i> </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group choose-verify-method">
                            <div class="ny-control-label">
                                <span class="necessary-mark">*</span> 套餐选择：
                            </div>
                            <div class="col-xs-9 ny-form-control validate-control">
                                <div class="ny-btn-group industry-tabs" id="">
                                    <div class="margin-bottom-10">
                                        <a href="javascript:" class="ny-btn"  style="color:#F00" id="base_one">基础版一个月：150元</a>
                                        <a href="javascript:" class="ny-btn"  style="color:#F00" id="base_two">基础版三个月：300</a>
                                        <a href="javascript:" class="ny-btn"  style="color:#F00" id="base_three">基础版十二个月：1500</a>
                                    </div>
                                    <div class="margin-bottom-10">
                                        <a href="javascript:" class="ny-btn"  style="color: #FC0" id="all_one">全能版一个月：230元</a>
                                        <a href="javascript:" class="ny-btn"  style="color: #FC0" id="all_two">全能版三个月：600</a>
                                        <a href="javascript:" class="ny-btn"  style="color: #FC0" id="all_three">全能版十二个月：5500</a>
                                    </div>
                                </div>
                                <input name="crmIndustry" value="" type="hidden" />
                                <span class="error-reminder"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="ny-control-label ny-label ny-label-darken payment-type">
                                扫码支付：
                            </div>
                            <div class="col-xs-10 ny-form-control">
                                <div class="payment-tier clearfix">
                                    <div class="payment-tier clearfix">
                                        <div class="">
                                            <input id="bocB2B" name="payType" value="BOCBTB" type="radio" />
                                            <label for="bocB2B" class="b2b-label-container" style="height: 200px;"> <img class="payment-choose payment-boc b2b-payment-boc" src="<?php echo $data['url']?>" id="erweima"/> <i></i> </label>
                                            <input type="text" hidden id="outTradeNo" value="<?php echo $data['outTradeNo'] ?>">
                                            <input type="text"  hidden id="type" value="<?php echo $data['type'] ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    $(function(){
        var outTradeNo = $("#outTradeNo").val();
        var type = $("#type").val();
        $("#base_one").css('border-color','rgb(0, 170, 255)');
        recheck(type,outTradeNo);

    });
    $(document).on("click", "#base_one", function () {
        $(".ny-btn").css('border-color','#D9D9D9');
        $("#base_one").css('border-color','rgb(0, 170, 255)');
        $("#type").val("base_one");
        renew("base_one");
    });
    $(document).on("click", "#base_two", function () {
        $(".ny-btn").css('border-color','#D9D9D9');
        $("#base_two").css('border-color','rgb(0, 170, 255)');
        $("#type").val("base_two");
        renew("base_two");
    });
    $(document).on("click", "#base_three", function () {
        $(".ny-btn").css('border-color','#D9D9D9');
        $("#base_three").css('border-color','rgb(0, 170, 255)');
        $("#type").val("base_three");
        renew("base_three");
    });
    $(document).on("click", "#all_one", function () {
        $(".ny-btn").css('border-color','#D9D9D9');
        $("#all_one").css('border-color','rgb(0, 170, 255)');
        $("#type").val("all_one");
        renew("all_one");
    });
    $(document).on("click", "#all_two", function () {
        $(".ny-btn").css('border-color','#D9D9D9');
        $("#all_two").css('border-color','rgb(0, 170, 255)');
        $("#type").val("all_two");
        renew("all_two");
    });

    $(document).on("click", "#all_three", function () {
        $(".ny-btn").css('border-color','#D9D9D9');
        $("#all_three").css('border-color','rgb(0, 170, 255)');
        $("#type").val("all_three");
        renew("all_three");
    });

    function recheck($id,$outTradeNo){

         var check = setInterval(function () {

                $.ajax({
                    url: "index.php?r=admin%2Fpassport%2Frenews",
                    type: "post",
                    dataType: "json",
                    data: {
                        out_trade_no:$outTradeNo,
                        type:$id,
                        _csrf: _csrf,
                    },
                    success: function (res) {
                        data = $("#type").val();

                        if(res.code == 0){
                            clearInterval(check);
                            alert(res.text);
                            location.href = "<?= \Yii::$app->urlManager->createUrl('admin/app/index') ?>";
                        }else if(data != $id){
                            clearInterval(check);
                        }
                    }
                });


        },5000);
    }
    function renew($id){
        $.ajax({
            url: '<?=Yii::$app->urlManager->createUrl(['admin/app/renew'])?>',
            type: "post",
            dataType: "json",
            data: {
                'type':$id,
            },
            complete: function (res) {

                $("#payAmount").text(res.responseJSON.payAmount+" 元");
                $("#erweima").attr('src',res.responseJSON.url);
                 recheck($id,res.responseJSON.outTradeNo);

            },
        });
    }
</script>
