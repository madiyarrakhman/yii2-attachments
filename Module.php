<?php

namespace mitrii\attachments;

use Imagine\Image\ImageInterface;
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
        'quality' => 90,
    ];

    public $cacheControlHeader = 'public, max-age=31536000'; // one year

    public $cache_resized = false;

    public $image_filter = ImageInterface::FILTER_UNDEFINED;

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

    public function getFilePath($attachment)
    {
        $attachment = ($attachment instanceof Attachment) ? $attachment : Attachment::findOne(['hash' => $attachment]);

        if (empty($attachment)) return null;

        return $this->getUploadPath().'/'.$attachment->path;
    }


}
