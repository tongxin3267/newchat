<div style="width: 80%;margin: 0 auto;font-size: 20px;">
    <div style="width:100%;margin-top: 10%;margin-left: 3%;">
        一级分销提成：&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" style="height: 80%;width: 10%;border: none;background-color: white;" disabled id="one"  value="<?php echo $one ?>">%
    </div>
    <div style="width:100%;margin-top: 6%;margin-left: 3%;">
        一级分销提成：&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" style="height: 80%;width: 10%;border: none;background-color: white;" disabled  id="two" value="<?php echo $two ?>">%
    </div>
    <div style="width:100%;margin-top: 3%;">
        <button id="edit" style="width:10%;margin-left: 3%;border: none;background-color: #55acee;color: white;font-size: 16px;">修改</button> <button id="sure" style="width:10%;margin-left: 5%;border: none;background-color: #55acee;color: white;font-size: 16px;">提交</button>
    </div>
</div>
<script>
    $("#edit").click(function () {
        $("#one").removeAttr('disabled');
        $("#two").removeAttr('disabled');
    });
    $("#sure").click(function () {
        var one = $("#one").val();
        var two = $("#two").val();

        $.post({
            url: "index.php?r=admin%2Fapp%2Froyalty",
            type: "post",
            dataType: "json",
            data: {
                one:one,
                two:two,
                _csrf: _csrf,
            },
            success: function (res) {
console.log(res);
                if(res == 0){
                    alert("设置成功");
                }else{
                    alert("设置失败");
                }
            }
        });
    });
</script>