<?php

namespace musan\attachments;

use Imagine\Image\ImageInterface;
use musan\attachments\components\AttachmentService;
use musan\attachments\components\UrlRule;
use musan\attachments\models\Attachment;
use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public $controllerNamespace = 'musan\attachments\controllers';

    /**
     * @var string Path for uploading files
     */
    public $upload_path = '';

    public $path_deep = 5;

    public $upload_roles = ['admin'];
    
    public $show_options = [
        'jpeg_quality' => 95,
        'png_compression_level' => 9
    ];

    public $cacheControlHeader = 'public, max-age=31536000'; // one year

    public $cache_resized = false;

    public $image_filter = ImageInterface::FILTER_UNDEFINED;

    public function init()
    {
        parent::init();

        $this->setComponents([
            'service' => [
                'class' => AttachmentService::class,
                '_module' => $this,
            ],
        ]);
        // custom initialization code goes here
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            [
                'class' => UrlRule::class,
                'moduleId' => $this->id,
            ],
        ], false);
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
