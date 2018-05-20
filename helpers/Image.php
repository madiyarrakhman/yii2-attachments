<?php

namespace musan\attachments\helpers;

use Yii;
use yii\helpers\Url;


class Image extends File
{
    static public function url($uid, $width, $height, $mode = null)
    {
        return Url::to(['/attachment/download/index', 'uid' => $uid, 'mode' => $mode, 'width' => $width, 'height' => $height]);

        //return Yii::$app->getModule('attachment')->get('render')->getImageUrl($uid, $width, $height, $mode);
    }
} 