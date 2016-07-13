<?php

namespace mitrii\attachments;

use mitrii\attachments\models\Attachment;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'mitrii\attachments\controllers';

    /**
     * @var string Path for uploading files
     */
    public $upload_path = '';

    public $path_deep = 5;

    public $upload_roles = ['admin'];
    
    public $show_options = [
        'jpeg_quality' => 75,
        'png_compression_level' => 9,
    ];

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    /**
     * @return string Path for uploaded files
     */
    public function getUploadPath()
    {
        return realpath($this->upload_path);
    }

    public function getPathDeep()
    {
        return $this->path_deep;
    }

    public function getFilePath($hash)
    {
        $attachment = Attachment::findOne(['hash' => $hash]);

        if (empty($attachment)) return null;

        return $this->getUploadPath().'/'.$attachment->path;
    }


}
