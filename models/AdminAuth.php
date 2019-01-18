<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%admin_register}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $mobile
 * @property string $name
 * @property string $desc
 * @property integer $addtime
 * @property integer $status
 * @property integer $is_delete
 */
class AdminAuth extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%weixin_account}}';
    }

    /**
     * @inheritdoc
     */


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '状态',
            'component_verify_ticket' => 'ticket码',
            'date_time' => '时间',

        ];
    }

}
