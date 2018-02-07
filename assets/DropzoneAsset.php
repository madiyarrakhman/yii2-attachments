<?php
/**
 * Poject: et
 * User: mitrii
 * Date: 9.09.2014
 * Time: 18:01
 * Original File Name: DropzoneAsset.php
 */

namespace musan\attachments\assets;


class DropzoneAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@musan/attachments/assets/dropzone';
    public $baseUrl = '@web';
    public $css = [
        'css/dropzone.css',
        'css/custom.css',
    ];
    public $js = [
        'dropzone.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

} 