<?php
/**
 * @author Lu Wei
 * Created by IntelliJ IDEA
 * Date Time: 2018/7/12 19:24
 */
namespace app\modules\admin\models;

use app\models\Admin;


class UserFocusForm extends AdminModel
{

    protected $appid = 'wxe0230f7649293d53';
    protected $secret = 'dbaead8bf57d9a48c75bfa05506df8a3';
    protected $url = "";
    protected $access_tokens = "";


    public function index(){
//        return "aa";
        $user_id = \Yii::$app->admin->id;
        $model = Admin::find()->where(['id' => $user_id])->one();
        if(!$model->openID){
            $this->actionSss();
            $qrcode = $this->Follow();
            return $this->render('focus',['qrcode'=>$qrcode]);
        }else{
           return "qq";
        }
    }

    public function actionSss()
    {
        //获取$access_token
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appid . "&secret=" . $this->secret . "";

        $result = $this->curl_post($url);

        $access_tokens = json_decode($result, true);
//        var_dump($access_tokens);exit;
        $this->access_tokens = $access_tokens['access_token'];

    }


    public function Follow(){

        $rs = $this->getTemporaryQrcode($this->access_tokens, 123);

        $ticket = $rs['ticket'];
        $qrcode = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $ticket . "";
        return $qrcode;

    }

    //生成二维码
    public function getTemporaryQrcode($access_tokens,$orderId)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" .$access_tokens . "";
//生成二维码需要的参数

        $qrcode = '{"expire_seconds": 1800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": ' . $orderId . '}}}';
        $momo = json_decode($qrcode, true);

        $result = $this->curl_post($url, $momo);

        $rs = json_decode($result, true);

        return $rs;
    }

    public function curl_post($url, array $params = array())
    {

        $data_string = json_encode($params);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json'
            )
        );
        $data = curl_exec($ch);

        curl_close($ch);
        return ($data);
    }
}
