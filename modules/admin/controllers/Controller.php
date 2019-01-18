<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/10/2
 * Time: 13:43
 */

namespace app\modules\admin\controllers;

use app\opening\Cloud;
use app\modules\admin\behaviors\AdminBehavior;
use app\modules\admin\behaviors\LoginBehavior;
use app\modules\mch\models\MchMenu;
use app\models\Admin;

class Controller extends \app\controllers\Controller
{
    protected $appid = '';
    protected $secret = '';
    protected $url = "";
    protected $access_tokens = "";
    public $layout = 'main';

    public $auth_info;

    public function init()
    {
        parent::init();
        Cloud::checkAuth();
        $this->auth_info = Cloud::getAuthInfo();
      
 		

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


    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'permission' => [
                'class' => AdminBehavior::className(),
            ],
            'login' => [
                'class' => LoginBehavior::className(),
            ],
        ]);
    }

    /**
     * 获取当前用户拥有的插件权限
     * @return mixed|null
     */
    public function getUserAuth()
    {
        $userAuth = json_decode(\Yii::$app->admin->identity->permission, true);

        return $userAuth;
    }
}
