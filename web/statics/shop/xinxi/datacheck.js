//非空验证
function SKECCheckIsEmpty(Id) {
    if ($.trim($("#" + Id).val()) == "") {
//        $("#" + Id).focus();
        return false;
    }
    return true;
}
//验证数组非空
function CheckIsEmpty(arr) {
    arr = arr.split(',');
    for (var i = 0; i < arr.length; i++) {
        if ($.trim($("#" + arr[i]).val()) == "") {
            $("#" + arr[i]).next().css("color", "red");
            $("#" + arr[i]).focus();
            return false;
        }
        else {
            $("#" + arr[i]).next().css("color", "#878787");
        }
    }
    return true;
}
//限制数值大小
//判断是否是汉字、字母、数字组成  
function f_check_ZhOrNumOrLett(Id) {
    var str = $("#" + Id).val();
    var regu = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！%@#￥……&*（）——|{}【】‘；：”“'。，、？]");
    if (regu.test(str)) {
        return true;
    } else {
        return false;
    }
}  
//整数
function isInteger(str) {
    var re = /^\d+$/;
    if (re.test(str)) {
        return true;
    }
    else {
        return false;
    }
}

//比较两个数
function CompareToAandB(Ida,Idb) {
    var valA = $("#" + Ida).val();
    var valB = $("#" + Idb).val();
    if (valA > valB) {
        return false;
    }
    else {
        return true;
    }

}
//检查输入字符串是否是带小数的数字格式,可以是负数 
function isDecimal(id) {
    var str = $("#" + id).val();
    if (isInteger(str)) {
        return true;
    }
    var re = /^[-]{0,1}(\d+)[\.]+(\d+)$/;
    if (re.test(str)) {
        if (RegExp.$1 == 0 && RegExp.$2 == 0) {
            $("#" + id).focus();
            return false;
        }
        else {
            return true;
        }
    } else {
        $("#" + id).focus();
        return false;
    }
}

//数字
function checkNum(id) {
    var str = $("#" + id).val();
    if (str.match(/\D/) == null) {
        return true;
    }
    else {
        $("#" + id).focus();
        return false;
    }
}

//手机
function checkMobile(id) {
    var mobile = $("#" + id).val();
    var regu = /^[1][0-9]{10}$/;
    var re = new RegExp(regu);
    if (re.test(mobile)) {
        $("#" + id).next().css("color", " ");
        return true;
    } else {
        $("#" + id).next().css("color", "red");
        return false;
    }
}

function IsMobile(id) {
    var mobile = $("#" + id).val();
    var regu = /^[1][0-9]{10}$/;
    var re = new RegExp(regu);
    if (re.test(mobile)) {
        return true;
    } else {
        return false;
    }
}

//验证固话或手机
function IsPhoneOrTelPhone(id) {
    var s = $("#" + id).val();
    var regu = /(^[0-9\-\s]{8,30}$)/;
    var re = new RegExp(regu);
    if (re.test(s)) {
        return true;
    } else {

        var regu = /^[1][0-9]{10}$/;
        var re = new RegExp(regu);
        if (re.test(s)) {
            return true;
        } else {
            $("#" + id).focus();
            return false;
        }
    }
}
//邮编
function IsPostCode(id) {
    var str = $("#" + id).val();
    if (str.match(/\D/) == null && str.length == 6) {
        $("#" + id).next().css("color", "");
        return true;
    }
    else {
        $("#" + id).val("");
        $("#" + id).next().css("color", "red");
    }
}


//邮箱
function isEmail(id) {
    var strEmail = $("#" + id).val();
    if (strEmail.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1) {
        $("#" + id).next().css("color", "");
        return true;
    }
    else {
        $("#" + id).next().css("color", "red");
        return false;
    }
}


//检查是否有字符串，数字，英文，组合或单个
function IsStringandNumberandAdC(id) {
    var ssn = $("#" + id).val();
    var re = /^[0-9a-zA-Zu4e00-\u9fa5\_]{6,20}$/;
    if (re.test(ssn)) {
        $("#" + id).next().css("color", " ");
        return true;
    }
    else {
        return false;
    }
}
//全选
$(function () {
    $("#ChkAll").click(function () {
        if ($(this).attr("checked") == true) { // 全选
            $("input").each(function () {
                $(this).attr("checked", true);
            });
        } else { // 取消全选
            $("input").each(function () {
                $(this).attr("checked", false);
            });
        }
    });
})

//检查输入字符串是否符合正整数格式
function isNumber(id) {
    var num = $("#" + id).val();
    var regu = "^[0-9]+$";
    var re = new RegExp(regu);
    if (num.search(re) != -1) {
        $("#" + id).next().css("color", " ");
        return true;
    } else {
        $("#" + id).next().css("color", "red");
        return false;
    }
}

//检查输入的是否是中文
function IsString(id) {
    var ssn = $("#" + id).val();
    var re = /[^u4e00-u9fa5]/g;
    var ret = new RegExp(re);
    if (ret.test(ssn)) {
        $("#" + id).next().css("color", " ");
        return true;
    } else {
        return false;
    }
}

//供应商注册密码校验
function chinaEnglish(obj) {
    var count = 0;
    var data = /[a-zA-Z]/;
    var shuzi = /[0-9]/;
    var teshu = /[^A-Za-z0-9_]/;

    if (data.test(obj)) {
        count++;
    }
    if (shuzi.test(obj)) {
        count++;
    }
    if (teshu.test(obj)) {
        count++;
    }
    if (count < 6) {
        return false;
    }
    else {
        return true;
    }
}