<?php

namespace mitrii\attachments\helpers;

use Yii;


class Image extends File
{
    static public function url($hash, $width, $height, $mode = null)
    {
        return Yii::$app->getModule('attachment')->get('render')->getImageUrl($hash, $width, $height, $mode);
    }
} 