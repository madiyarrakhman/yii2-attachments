<?php

namespace musan\attachments\widgets\imageuploader;

use yii\web\AssetBundle;

/**
 * @deprecated
 */
class DropzoneAsset extends AssetBundle
{
    public $sourcePath = '@npm/dropzone/dist';
    public $css = [
        //'basic.css',
        //'dropzone.css',
    ];
    public $js = [
        'dropzone.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
