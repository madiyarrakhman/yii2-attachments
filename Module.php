<?php

namespace mitrii\attachments;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'mitrii\attachments\controllers';

    /**
     * @var string Path for uploading files
     */
    public $upload_path = '';

    public $path_deep = 5;

    public $upload_roles = ['admin'];

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


}
