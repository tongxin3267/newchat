<?php

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/10/3
 * Time: 11:34
 */

namespace app\modules\mch\controllers;

use app\opening\CloudAdmin;
use app\models\Admin;
use app\models\AdminRegister;
use app\modules\admin\models\UserRegisterForm;
use app\modules\admin\models\UserFocusForm;
use yii\data\Pagination;
use app\models\Audit;
use app\models\AdminAuth;
use app\models\Apply;
use app\models\Store;
use app\models\Release;
use app\models\Distribution;

class ApplyController extends Controller
{
    protected $appid = 'wx2bdc1845f7e64617';            //第三方平台应用appid

    private $appsecret = '53905d776670e2ef1bca0ec064143e9d';     //第三方平台应用appsecret

    private $token = 'weixin';           //第三方平台应用token（消息校验Token）

    private $encodingAesKey = 'oiO8KM82sa05W4S1BpGgROnx64QpzeW5KJNur6P7VZG';      //第三方平台应用Key（消息加解密Key）

    private $component_ticket = '';   //微信后台推送的ticket,用于获取第三方平台接口调用凭据

    private $component_access_token;  //第三方平台component_access_token

    private $authorizer_appid;

    private $authorizer_access_token;

    private $authorizer_refresh_token;



    public function actionIndex()
    {
       		$data = Apply::find()->where(['uid'=>\Yii::$app->admin->id])->one();
            $this->authorizer_refresh_token = $data->authorizer_refresh_token;
            $this->authorizer_access_token = $data->authorizer_access_token;
            $this->authorizer_appid = $data->authorizer_appid;
            $datas = AdminAuth::find()->where(['id'=>1])->one();
            $this->component_access_token = $datas->component_access_token;

   
        if($data->authorizer_expires < time()){
			
          $this->update_authorizer_access_token($data->authorizer_appid,$data->authorizer_refresh_token);
          $data = Apply::find()->where(['uid'=>\Yii::$app->admin->id])->one();
            $this->authorizer_refresh_token = $data->authorizer_refresh_token;
            $this->authorizer_access_token = $data->authorizer_access_token;
            $this->authorizer_appid = $data->authorizer_appid;
            $datas = AdminAuth::find()->where(['id'=>1])->one();
            $this->component_access_token = $datas->component_access_token;

        }
	//$this->unDoCodeAudit();

       if (\Yii::$app->request->isPost) {
       		
            //设置小程序服务器地址
            $res_1 = $this->setServerDomain();
    
            if($res_1['errcode'] == 85017){
                $data = '{

                    "action":"get"

                    }';
                $ret = $this->setServerDomain($domain = 'www.oggogg.com',$res = $data);
         
                foreach ($ret->requestdomain as $k=>$v){
                    if($v == 'https://www.oggogg.com'){
                        $ret_1 = true;continue;
                    }
                }
              
                foreach ($ret->wsrequestdomain as $k=>$v){
                    if($v == 'wss://www.oggogg.com'){
                        $ret_2 = true;continue;
                    }
                }
                foreach ($ret->uploaddomain as $k=>$v){
                    if($v == 'https://www.oggogg.com'){
                        $ret_3 = true;continue;
                    }
                }
                foreach ($ret->downloaddomain as $k=>$v){
                    if($v == 'https://www.oggogg.com'){
                        $ret_4 = true;continue;
                    }
                }
            
                if($ret_1 && $ret_2 && $ret_3 && $ret_4){
                    $step_1 = true;
                }else{
                    return $res_1;
                }

            }
         
            if($res_1 != 0 && $res_1['errcode'] != 85017){
                return $res_1;
            }
          
            //设置小程序业务域名
           $res_2 = $this->setBusinessDomain();

            if($res_2 != 0){
                return $res_2;
            }
            //为授权的小程序帐号上传小程序代码
            $res_3 = $this->uploadCode($template_id = 4, $user_version = '1.0.0', $user_desc = "黑虎");
            if($res_3 != 0){
                return $res_3;
            }
       
            //提交审核
            $res_4 = $this->submitReview($tag = "黑虎小程序商城" ,$title = "黑虎小程序开发");
      
                return $res_4;
      }
        

    }



    /*

    * 设置小程序服务器地址，无需加https前缀，但域名必须可以通过https访问

    * @params string / array $domains : 域名地址。只接收一维数组。

    * */

    public  function setServerDomain($domain = 'www.oggogg.com',$res = '')

    {


        $url = "https://api.weixin.qq.com/wxa/modify_domain?access_token=".$this->authorizer_access_token;

        if($res){
            $data = $res;
            $ret = json_decode($this->https_post($url,$data));
            return $ret;
        }else{
            $data = '{

                "action":"add",

                "requestdomain":"https://'.$domain.'",

                "wsrequestdomain":"wss://'.$domain.'",

                "uploaddomain":"https://'.$domain.'",

                "downloaddomain":"https://'.$domain.'"

            }';
            $ret = json_decode($this->https_post($url,$data));

            if($ret->errcode == 0) {

                return $ret->errcode;

            } else {
                $result =array();
                $result['errcode'] = $ret->errcode;
                if($ret->errcode == 85015){
                    $result['errmsg'] = "该账号不是小程序账号";
                }
                if($ret->errcode == 85016){
                    $result['errmsg'] = "域名数量超过限制";
                }
                if($ret->errcode == 85017){
                    $result['errmsg'] = "没有新增域名，请确认小程序已经添加了域名或该域名是否没有在第三方平台添加";
                }
                if($ret->errcode == 85018){
                    $result['errmsg'] = "域名没有在第三方平台设置";
                }

                $this->errorLog("设置小程序服务器地址失败,appid:".$this->authorizer_appid,$ret);

                return $result;

            }
        }



    }
    /*

    * 设置小程序业务域名，无需加https前缀，但域名必须可以通过https访问

    * @params string / array $domains : 域名地址。只接收一维数组。

    * */

    public function setBusinessDomain($domain = 'www.oggogg.com')

    {

        $url = "https://api.weixin.qq.com/wxa/setwebviewdomain?access_token=".$this->authorizer_access_token;

        if(is_array($domain)) {

            $https = '';

            foreach ($domain as $key => $value) {

                $https .= '"https://'.$value.'",';

            }

            $https = rtrim($https,',');

            $data = '{

                "action":"add",

                "webviewdomain":['.$https.']

            }';

        } else {

            $data = '{

                "action":"add",

                "webviewdomain":"https://'.$domain.'"

            }';

        }



        $ret = json_decode($this->https_post($url,$data));

        if($ret->errcode == 0) {

            return $ret->errcode;

        } else {

            $result =array();
            $result['errcode'] = $ret->errcode;
            if($ret->errcode == 89019){
                $result['errmsg'] = "业务域名无更改，无需重复设置";
            }
            if($ret->errcode == 89020){
                $result['errmsg'] = "尚未设置小程序业务域名，请先在第三方平台中设置小程序业务域名后在调用本接口";
            }
            if($ret->errcode == 89021){
                $result['errmsg'] = "请求保存的域名不是第三方平台中已设置的小程序业务域名或子域名";
            }
            if($ret->errcode == 89029){
                $result['errmsg'] = "业务域名数量超过限制";
            }
            if($ret->errcode == 89231){
                $result['errmsg'] = "个人小程序不支持调用setwebviewdomain 接口";
            }

            $this->errorLog("设置小程序业务域名失败,appid:".$this->authorizer_appid,$ret);

            return $result;





        }

    }

    /*

     * 为授权的小程序帐号上传小程序代码

     * @params int $template_id : 模板ID

     * @params json $ext_json : 小程序配置文件，json格式

     * @params string $user_version : 代码版本号

     * @params string $user_desc : 代码描述

     * */

      public function uploadCode($template_id, $user_version, $user_desc)

    {
        $data = Store::find()->where(['admin_id'=>\Yii::$app->admin->id])->one();

       $ext_json = json_encode('{"extEnable": true,"extAppid": "wx2bdc1845f7e64617","ext":{"appid": "'.$this->authorizer_appid.'","xcxid": "'.$data->id.'"}}');
        
  
        
        $url = "https://api.weixin.qq.com/wxa/commit?access_token=".$this->authorizer_access_token;

        $data = '{"template_id":"'.$template_id.'","ext_json":'.$ext_json.',"user_version":"'.$user_version.'","user_desc":"'.$user_desc.'"}';

        $ret = json_decode($this->https_post($url,$data));


        if($ret->errcode == 0) {

            return $ret->errcode;

        } else {

            $result =array();
            $result['errcode'] = $ret->errcode;
            if($ret->errcode == -1){
                $result['errmsg'] = "系统繁忙";
            }
            if($ret->errcode == 85013){
                $result['errmsg'] = "无效的自定义配置";
            }
            if($ret->errcode == 85014){
                $result['errmsg'] = "无效的模版编号";
            }
            if($ret->errcode == 85043){
                $result['errmsg'] = "模版错误";
            }
            if($ret->errcode == 85044){
                $result['errmsg'] = "代码包超过大小限制";
            }
            if($ret->errcode == 85045){
                $result['errmsg'] = "ext_json有不存在的路径";
            }
            if($ret->errcode == 85046){
                $result['errmsg'] = "tabBar中缺少path";
            }
            if($ret->errcode == 85047){
                $result['errmsg'] = "pages字段为空";
            }
            if($ret->errcode == 85048){
                $result['errmsg'] = "ext_json解析失败";
            }
            $this->errorLog("为授权的小程序帐号上传小程序代码操作失败,appid:".$this->authorizer_appid,$ret);

            return $result;

        }

    }

    /*

        * 获取体验小程序的体验二维码

        * @params string $path :   指定体验版二维码跳转到某个具体页面

        * */

    public function actionVersion($path = '')

    {
      	
       $data = Apply::find()->where(['uid'=>\Yii::$app->admin->id])->one();


         $url = "https://api.weixin.qq.com/wxa/get_qrcode?access_token=".$data->authorizer_access_token;

       

        $ret = json_decode($this->https_get($url));

        if($ret->errcode) {

            $this->errorLog("获取体验小程序的体验二维码操作失败,appid:".$this->authorizer_appid,$ret);

            return false;

        } else {

            return $url;

        }

    }

    /*

     * 提交审核

     * @params string $tag : 小程序标签，多个标签以空格分开

     * @params strint $title : 小程序页面标题，长度不超过32

     * */

       public function submitReview($tag ,$title)

    {

        $first_class = '';$second_class = '';$first_id = 0;$second_id = 0;

        $address = "pages/index/index";

        $category = $this->getCategory();

        if(!empty($category)) {

            $first_class = $category[0]->first_class ? $category[0]->first_class : '' ;

            $second_class = $category[0]->second_class ? $category[0]->second_class : '';

            $first_id = $category[0]->first_id ? $category[0]->first_id : 0;

            $second_id = $category[0]->second_id ? $category[0]->second_id : 0;

        }

        $getpage = $this->getPage();

        if(!empty($getpage) && isset($getpage[0])) {

            $address = $getpage[0];

        }

        $url = "https://api.weixin.qq.com/wxa/submit_audit?access_token=".$this->authorizer_access_token;

        $data = '{

                "item_list":[{

                    "address":"'.$address.'",

                    "tag":"'.$tag.'",

                    "title":"'.$title.'",

                    "first_class":"'.$first_class.'",

                    "second_class":"'.$second_class.'",

                    "first_id":"'.$first_id.'",

                    "second_id":"'.$second_id.'"

                }]

            }';

        $ret = json_decode($this->https_post($url,$data));

        if($ret->errcode == 0) {

            $data = Audit::find()->where(['appid'=>$this->authorizer_appid])->one();
    
                $data->auditid = $ret->auditid;
                $data->status = 3;
                $data->create_time = date('Y-m-d H:i:s');
                $data->save();
            
            return $ret->errcode;

        } else {

            $result =array();
            $result['errcode'] = $ret->errcode;
            if($ret->errcode == -1){
                $result['errmsg'] = "系统繁忙";
            }
            if($ret->errcode == 86000){
                $result['errmsg'] = "不是由第三方代小程序进行调用";
            }
            if($ret->errcode == 86001){
                $result['errmsg'] = "不存在第三方的已经提交的代码";
            }
            if($ret->errcode == 85006){
                $result['errmsg'] = "标签格式错误";
            }
            if($ret->errcode == 85007){
                $result['errmsg'] = "页面路径错误";
            }
            if($ret->errcode == 85008){
                $result['errmsg'] = "类目填写错误";
            }
            if($ret->errcode == 85009){
                $result['errmsg'] = "已经有正在审核的版本";
            }
            if($ret->errcode == 85010){
                $result['errmsg'] = "item_list有项目为空";
            }
            if($ret->errcode == 85011){
                $result['errmsg'] = "标题填写错误";
            }
            if($ret->errcode == 85023){
                $result['errmsg'] = "审核列表填写的项目数不在1-5以内";
            }
            if($ret->errcode == 85077){
                $result['errmsg'] = "小程序类目信息失效（类目中含有官方下架的类目，请重新选择类目）";
            }
            if($ret->errcode == 86002){
                $result['errmsg'] = "小程序还未设置昵称、头像、简介。请先设置完后再重新提交";
            }
            if($ret->errcode == 85085){
                $result['errmsg'] = "近7天提交审核的小程序数量过多，请耐心等待审核完毕后再次提交";
            }
            if($ret->errcode == 85086){
                $result['errmsg'] = "提交代码审核之前需提前上传代码";
            }
            if($ret->errcode == 85087){
                $result['errmsg'] = "小程序已使用api navigateToMiniProgram，请声明跳转appid列表后再次提交";
            }
            $this->errorLog("小程序提交审核操作失败，appid:".$this->authorizer_appid,$ret);

            return $result;

        }

    }
  
    /*

     * 小程序审核撤回

     * 单个帐号每天审核撤回次数最多不超过1次，一个月不超过10次。

     * */

    public function unDoCodeAudit()

    {

        $url = "https://api.weixin.qq.com/wxa/undocodeaudit?access_token=".$this->authorizer_access_token;

        $ret = json_decode($this->https_get($url));
		dd($ret);
        if($ret->errcode == 0) {

            return true;

        } else {

            $this->errorLog("小程序审核撤回操作失败，appid:".$this->authorizer_appid,$ret);

            return false;

        }

    }
  
  


    /*

     * 发布已通过审核的小程序

     * */

    public function actionRelease()

    {
      
        $data = Apply::find()->where(['uid'=>\Yii::$app->admin->id,'is_delete'=>0])->one();

        $url = "https://api.weixin.qq.com/wxa/release?access_token=".$data->authorizer_access_token;

        $data = '{}';

        $ret = json_decode($this->https_post($url,$data));
      

      if($ret->errcode == 0){
			Audit::updateAll(['status'=>5],['appid'=>$data->authorizer_appid]);
			$data = Admin::find()->where(['id'=>\Yii::$app->admin->id])->one();
			if($data->release_xcx == 1){
                $data->release_xcx = 0;
                $data->save();
            }
            $res = Release::find()->where(['user_id'=>\Yii::$app->admin->id])->one();
            $res->status = 1;
            $res->time = time();

            $distribution = Distribution::find()->where(['son_id'=>\Yii::$app->admin->id])->one();
            $num = count(Release::find()->where(['father_id'=>$distribution->father_id,'reward'=>1])->all());
            if($num <= 5){
                $res->reward = 1;
                $re = Admin::find()->where(['id'=>$distribution->father_id])->one();
                if($re->level != 0){
                    $re->expire_time = strtotime("+1 months",$re->expire_time);
                    $re->save();
                }
            }
            $res->save();

        }
      

        $result =array();
        $result['errcode'] = $ret->errcode;
        if($ret->errcode == -1){
            $result['errmsg'] = "系统繁忙";
        }
        if($ret->errcode == 85019){
            $result['errmsg'] = "没有审核版本";
        }
        if($ret->errcode == 85020){
            $result['errmsg'] = "审核状态未满足发布";
        }

        return $result;
    }

    /*

     * 获取授权小程序帐号的可选类目

     * */

    private function getCategory()

    {

        $url = "https://api.weixin.qq.com/wxa/get_category?access_token=".$this->authorizer_access_token;

        $ret = json_decode($this->https_get($url));

        if($ret->errcode == 0) {

            return $ret->category_list;

        } else {

            $this->errorLog("获取授权小程序帐号的可选类目操作失败，appid:".$this->authorizer_appid,$ret);

            return false;

        }

    }

    /*

     * 获取小程序的第三方提交代码的页面配置

     * */

    private function getPage()

    {

        $url = "https://api.weixin.qq.com/wxa/get_page?access_token=".$this->authorizer_access_token;

        $ret = json_decode($this->https_get($url));

        if($ret->errcode == 0) {

            return $ret->page_list;

        } else {

            $this->errorLog("获取小程序的第三方提交代码的页面配置失败，appid:".$this->authorizer_appid,$ret);

            return false;

        }

    }

    /*

    * 更新授权小程序的authorizer_access_token

    * @params string $appid : 小程序appid

    * @params string $refresh_token : 小程序authorizer_refresh_token

    * */

    private function update_authorizer_access_token($appid,$refresh_token)

    {

        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=' . $this->component_access_token;

        $data = '{"component_appid":"' . $this->appid . '","authorizer_appid":"' . $appid . '","authorizer_refresh_token":"' . $refresh_token . '"}';

        $ret = json_decode($this->https_post($url, $data));

        if (isset($ret->authorizer_access_token)) {
            $data = Apply::find()->where(['uid'=>\Yii::$app->admin->id])->one();
            $data->authorizer_access_token = $ret->authorizer_access_token;
            $data->authorizer_expires = time() + 7200;
            $data->authorizer_refresh_token = $ret->authorizer_refresh_token;
            $data->save();
//            Apply::find()->updateAll(['authorizer_appid' => $appid], ['authorizer_access_token' => $ret->authorizer_access_token, 'authorizer_expires' => (time() + 7200), 'authorizer_refresh_token' => $ret->authorizer_refresh_token]);

            return $ret;

        } else {

            $this->errorLog("更新授权小程序的authorizer_access_token操作失败,appid:".$appid,$ret);

            return null;

        }

    }



    private function errorLog($msg,$ret)

    {

        file_put_contents('miniprogram.log', "[" . date('Y-m-d H:i:s') . "] ".$msg."," .json_encode($ret).PHP_EOL, FILE_APPEND);

    }

    /*

* 发起POST网络提交

* @params string $url : 网络地址

* @params json $data ： 发送的json格式数据

*/

    private function https_post($url,$data)

    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        if (!empty($data)){

            curl_setopt($curl, CURLOPT_POST, 1);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($curl);

        curl_close($curl);

        return $output;

    }

    /*

   * 发起GET网络提交

   * @params string $url : 网络地址

   */

    private function https_get($url)

    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($curl, CURLOPT_HEADER, FALSE) ;

        curl_setopt($curl, CURLOPT_TIMEOUT,60);

        if (curl_errno($curl)) {

            return 'Errno'.curl_error($curl);

        }

        else{$result=curl_exec($curl);}

        curl_close($curl);

        return $result;

    }
}
