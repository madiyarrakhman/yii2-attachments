<?php

namespace musan\attachments\helpers;

use Yii;


class Audio extends File
{
    static public function url($hash)
    {
        return Yii::$app->getModule('attachment')->get('render')->getAudioUrl($hash);
    }
}