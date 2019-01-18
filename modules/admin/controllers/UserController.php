<?php

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/10/3
 * Time: 11:34
 */

namespace app\modules\admin\controllers;

use app\opening\CloudAdmin;
use app\models\Admin;
use app\models\AdminRegister;
use app\modules\admin\models\UserRegisterForm;
use app\modules\admin\models\UserFocusForm;
use yii\data\Pagination;
use app\modules\admin\controllers\ReceiveController;

class UserController extends Controller
{
    protected $appid = '';
    protected $secret = '';
    protected $url = "";
    protected $access_tokens = "";
    protected $ticket = "";


    public function actionIndex()
    {
        $query = Admin::find()->where(['is_delete' => 0]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count]);
        $list = $query->limit($pagination->limit)->offset($pagination->offset)->orderBy('addtime DESC')->all();

            return $this->render('index', [
                'list' => $list,
                'pagination' => $pagination,
            ]);


        }



    public function actionEdit($id = null)
    {
        if (\Yii::$app->request->isPost) {
            return CloudAdmin::saveEditUserData();
        } else {
            $data = CloudAdmin::getEditUserData();
            return $this->render('edit', $data);
        }
    }

    public function actionModifyPassword($id)
    {
        $admin = Admin::findOne([
            'id' => $id,
            'is_delete' => 0,
        ]);
        if (!$admin) {
            return [
                'code' => 1,
                'msg' => '用户不存在，请刷新页面后重试',
            ];
        }

        $paswword = \Yii::$app->request->post('password');
        if (strlen($paswword) == 0) {
            return [
                'code' => 1,
                'msg' => '密码不能为空',
            ];
        }

        $admin->password = \Yii::$app->security->generatePasswordHash($paswword);
        $admin->auth_key = \Yii::$app->security->generateRandomString();
        $admin->access_token = \Yii::$app->security->generateRandomString();
        if ($admin->save()) {
            return [
                'code' => 0,
                'msg' => '修改密码成功',
            ];
        } else {
            return [
                'code' => 1,
                'msg' => '修改密码失败',
            ];
        }
    }

    public function actionDelete($id)
    {
        if (!\Yii::$app->request->isPost) {
            return;
        }

        $admin = Admin::findOne([
            'id' => $id,
            'is_delete' => 0,
        ]);
        if (!$admin) {
            return [
                'code' => 1,
                'msg' => '用户不存在，请刷新页面后重试',
            ];
        }

        $admin->is_delete = 1;
        if ($admin->save()) {
            return [
                'code' => 0,
                'msg' => '删除用户成功',
            ];
        }

        return [
            'code' => 1,
            'msg' => '删除用户失败',
        ];
    }

    public function actionMe()
    {
          
            return $this->render('me');

    }

    //注册审核
    public function actionRegister($status = 0)
    {
        if (\Yii::$app->request->isPost) {

            $form = new UserRegisterForm();
            $form->attributes = \Yii::$app->request->post();
            return $form->save();
        } else {
            $query = AdminRegister::find()->where(['is_delete' => 0, 'status' => $status]);
            $count = $query->count();
            $pagination = new Pagination(['totalCount' => $count,]);
            $list = $query->limit($pagination->limit)->offset($pagination->offset)->orderBy('addtime DESC')->all();
            return $this->render('register', [
                'list' => $list,
                'pagination' => $pagination,
            ]);
        }
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
