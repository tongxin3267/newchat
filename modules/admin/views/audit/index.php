<?php
?>
<div class="mb-3">
  <div>
      <span>第一步</span>
      <a href="javascript:"><button id="first" >上传代码</button></a>
  </div>
</div>
<script>

    $(document).on("click", "#first", function () {
        $.post({
            url: "index.php?r=admin%2Fapply%2Findex",
            type: "post",
            dataType: "json",
            data: [],
            complete:function(res){
  				console.log(res);
            },
            success:function (res) {
                console.log(res);
            }

        });
    });
</script>