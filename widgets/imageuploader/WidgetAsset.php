<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace musan\attachments\widgets\imageuploader;

use yii\web\AssetBundle;

/**
 * @deprecated
 */
class WidgetAsset extends AssetBundle
{
    public $sourcePath = '@musan/attachments/widgets/imageuploader/assets';
    public $css = [
        'basic.css',
        'dropzone.css',
    ];
    public $js = [
        'photouploader.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
