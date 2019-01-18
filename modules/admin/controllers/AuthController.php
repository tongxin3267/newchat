<?php
namespace app\modules\admin\controllers;

header("Content-type: text/html; charset=utf-8");

include "wxBizMsgCrypt.php";
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/10/3
 * Time: 11:34
 */
use app\models\AdminAuth;
use app\models\Store;
use app\models\WechatApp;
use app\models\Apply;

class AuthController extends Controller

{

    public function actionIndex()

    {
      
 
        $appid = 'wx2bdc1845f7e64617';  //第三方平台应用appid
        $appsecret ='53905d776670e2ef1bca0ec064143e9d';     //第三方平台应用appsecret
        $token = 'weixin';           //第三方平台应用token（消息校验Token）
        $encodingAesKey = 'oiO8KM82sa05W4S1BpGgROnx64QpzeW5KJNur6P7VZG';      //第三方平台应用Key（消息加解密Key）

        $timeStamp  = empty($_GET['timestamp'])     ? ""    : trim($_GET['timestamp']) ;
        $nonce      = empty($_GET['nonce'])     ? ""    : trim($_GET['nonce']) ;
        $msg_sign   = empty($_GET['msg_signature']) ? ""    : trim($_GET['msg_signature']) ;

        $encryptMsg = file_get_contents('php://input');

        $pc = new \WXBizMsgCrypt( $token, $encodingAesKey, $appid );
        $xml_tree = new \DOMDocument();
        $xml_tree->loadXML($encryptMsg);
        $array_e = $xml_tree->getElementsByTagName('Encrypt');
        $encrypt = $array_e->item(0)->nodeValue;

        $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $encrypt);

        // 第三方收到公众号平台发送的消息
        $msg = '';
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
     

        if ($errCode == 0)
        {
            $xml = new \DOMDocument();
            $xml->loadXML($msg);
            $array_type = $xml->getElementsByTagName('InfoType');
            $InfoType = $array_type->item(0)->nodeValue;
            switch ( $InfoType )
            {
                case 'component_verify_ticket': //请求ticket
                    $array_e = $xml->getElementsByTagName('ComponentVerifyTicket');
                    $component_verify_ticket = $array_e->item(0)->nodeValue;
                    //保存

                    AdminAuth::updateAll(['component_verify_ticket'=>$component_verify_ticket],['id'=>1]);
                    $this->updateAccessToken($component_verify_ticket);
 
                    break;
                case 'unauthorized':   //取消授权
                    $array_appid = $xml->getElementsByTagName('AuthorizerAppid');
                    $authorizer_appid = $array_appid->item(0)->nodeValue;
        
                    //这里写你的业务
                  	$data_app = WechatApp::find()->where(['app_id'=>$authorizer_appid])->one();
                   
                    $res_1 = Store::updateAll(['is_delete'=>1],['wechat_app_id'=>$data_app->id]);
                   $res_2 = WechatApp::updateAll(['is_delete'=>1],['app_id'=>$authorizer_appid]);
                   $res_3 = Apply::updateAll(['is_delete'=>1],['authorizer_appid'=>$authorizer_appid]);
               
                    break;
                case 'updateauthorized'://更新授权
                    $array_code = $xml->getElementsByTagName('AuthorizationCode');
                    $code = $array_code->item(0)->nodeValue;
                    //授权code 这里写你的业务
         
                    break;
                default:
                    echo "false"; die();
                    break;
            }
            echo 'success';
        } else
        {
            echo "false";
        }

    }

    /*

     * 更新第三方平台的component_access_token

     * @params string $component_verify_ticket

     * */

    private function updateAccessToken($component_verify_ticket)

    {

        $weixin_account = AdminAuth::find()->where(['id'=>1])->one();

        if($weixin_account['token_expires'] <= time() ) {

            $url =  'https://api.weixin.qq.com/cgi-bin/component/api_component_token';

            $data = '{

            "component_appid":"'.$weixin_account['appid'].'" ,

            "component_appsecret": "'.$weixin_account['appsecret'].'",

            "component_verify_ticket": "'.$component_verify_ticket.'"

        }';

            $json = json_decode($this->https_post($url,$data));
		
            if(isset($json->component_access_token)) {

                $ticket = AdminAuth::find()->where(['id'=>1])->one();
                $ticket->component_access_token = $json->component_access_token;
                $ticket->token_expires = time()+7200;
                $ticket->save();

            }
        }

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
}