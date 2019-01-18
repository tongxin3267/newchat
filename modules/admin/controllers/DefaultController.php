<?php

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2017/10/2
 * Time: 13:43
 */

namespace app\modules\admin\controllers;

use app\models\Admin;

use app\opening\CloudAdmin;
use app\models\AdminRegister;
use app\modules\admin\models\UserRegisterForm;
use yii\data\Pagination;
use app\models\Goods;
use app\models\Model;
use app\models\Order;
use app\models\User;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        if (\Yii::$app->request->isPost) {
            $orderlist = $datelist = $userlist = $applist = [];
            $days = \Yii::$app->request->post('days');
            for ($i = 0; $i < $days; $i++) {
                $startTime = strtotime(date('Y-m-d 00:00:00') . ' -' . $i . ' days');
                $endTime = strtotime(date('Y-m-d 23:59:59') . ' -' . $i . ' days');
                $date = date('Y-m-d', $startTime);
                $query = Order::find()->where([
                    'is_delete' => Order::IS_DELETE_FALSE,
                    'is_cancel' => Order::IS_CANCEL_FALSE,
                ])->andWhere(['or', ['is_pay' => Order::IS_PAY_TRUE], ['pay_type' => Order::PAY_TYPE_COD]]);
                if ($startTime !== null) {
                    $query->andWhere(['>=', 'addtime', $startTime]);
                }
                if ($endTime !== null) {
                    $query->andWhere(['<=', 'addtime', $endTime]);
                }

                $orders = $query->all();

                $orderlist[] = count($orders);

                $query = User::find()->where([
                    'is_delete' => 0,
                ])->andWhere(['<=', 'addtime', $endTime]);

                $users = $query->all();

                $userlist[] = count($users);

                $query = Admin::find()->where(['is_delete' => 0])->andWhere(['<=', 'addtime', $endTime]);
                $applist[] = $query->count();
                $datelist[] = $date;
            }
            $arr= array(
                'orderlist' => array_reverse($orderlist),
                'userlist' => array_reverse($userlist),
                'applist' => array_reverse($applist),
                'datelist' => array_reverse($datelist));

            return [
                'code' => 0,
                'data' => $arr,
            ];
        }else{
            $admin = \Yii::$app->admin->identity;
            if($admin->username!='admin'){
                return $this->render('index');
            }else{
                $goods_list = Goods::find()->select('id,attr')->all();
                $goods_count = count($goods_list);
                $orders_list = Order::find()->select('*')->all();
                $order_count = count($orders_list);
                $users_list = User::find()->select('*')->all();
                $user_count = count($users_list);
                $app_count = Admin::find()->where(['is_delete' => 0])->count();
                return $this->render('index2', [
                    'goods_count' => $goods_count,
                    'order_count'=>$order_count,
                    'user_count'=>$user_count,
                    'app_count'=>$app_count
                ]);
            }
        }
    }

    public function actionAlterPassword()
    {
        if (\Yii::$app->request->isPost) {
            /* @var  Admin $admin */
            $admin = \Yii::$app->admin->identity;
            $old_password = \Yii::$app->request->post('old_password');
            $new_password = \Yii::$app->request->post('new_password');
            if ($old_password == "" || $new_password == "") {
                return [
                    'code' => 1,
                    'msg' => '原密码和新密码不能为空',
                ];
            }
            if (!\Yii::$app->security->validatePassword($old_password, $admin->password)) {
                return [
                    'code' => 1,
                    'msg' => '原密码不正确',
                ];
            }
            $admin->password = \Yii::$app->security->generatePasswordHash($new_password);
            if ($admin->save()) {
                \Yii::$app->admin->logout();
                return [
                    'code' => 0,
                    'msg' => '修改成功',
                ];
            } else {
                return [
                    'code' => 0,
                    'msg' => '修改失败',
                ];
            }
        }
    }
}
