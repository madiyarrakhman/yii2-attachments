<?php

namespace frontend\widgets\PhotoUploader;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DropzoneAsset extends AssetBundle
{
    public $sourcePath = '@bower/dropzone/dist';
    public $css = [
        'basic.css',
        'dropzone.css',
    ];
    public $js = [
        'dropzone.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
