<?php

namespace musan\attachments\helpers;

use Yii;

/**
 * Class Audio
 * @deprecated Use Url::toFile() insteed
 */
class Audio extends File
{
    static public function url($uid)
    {
        return Yii::$app->getModule('attachment')->get('render')->getAudioUrl($uid);
    }
}