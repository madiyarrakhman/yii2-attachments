<?php

namespace mitrii\attachments\helpers;

use mitrii\attachments\models\Attachment;
use Yii;


class File
{
    /**
     * @param $attachment string|Attachment
     * @return string
     */
    static public function path($attachment)
    {
        return Yii::$app->getModule('attachment')->getFilePath($attachment);
    }
}