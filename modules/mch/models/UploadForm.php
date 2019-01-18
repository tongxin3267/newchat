<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/30
 * Time: 16:12
 */

namespace app\modules\mch\models;

use yii\base\Model;
use yii\web\UploadedFile;
use app\Models\WechatApp;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png'],
        ];
    }

    public function upload()
    {
    }
   
}