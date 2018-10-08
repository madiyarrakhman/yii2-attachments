<?php

namespace musan\attachments\helpers;

use musan\attachments\models\Attachment;
use Yii;

/**
 * Class File
 *
 */
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

    /**
     * @deprecated Use Url::toFile() insteed
     */
    static public function url($uid)
    {
        return Url::toFile($uid);
    }
}