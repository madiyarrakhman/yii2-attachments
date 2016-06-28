<?php

namespace mitrii\attachments\helpers;

use Yii;


class File
{
    static public function path($hash)
    {
        return Yii::$app->getModule('attachment')->getFilePath($hash);
    }
}