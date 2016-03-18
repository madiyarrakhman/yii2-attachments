<?php

namespace mitrii\attachments\helpers;

use Yii;


class Audio
{
    static public function url($hash)
    {
        return Yii::$app->getModule('attachment')->get('render')->getAudioUrl($hash);
    }
}