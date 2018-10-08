<?php

namespace musan\attachments\helpers;

use Yii;

/**
 * Class Image
 * @deprecated Use Url::toImage() insteed
 */
class Image
{
    static public function url($uid, $width, $height, $mode = null)
    {
        return Url::toImage($uid, $width, $height, $mode);
    }
} 