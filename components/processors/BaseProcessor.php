<?php
namespace musan\attachments\components\processors;

use musan\attachments\components\AttachmentService;
use musan\attachments\models\Attachment;
use yii\base\BaseObject;

/**
 * Class BaseProcessor
 * @package musan\attachments\components\processors
 * @property-read AttachmentService $service
 */
abstract class BaseProcessor extends BaseObject
{

    const REQUIRED_PARAMS = [];

    /**
     * @var string[] Processor extensions list
     */
    public $extensions;

    /**
     * @var AttachmentService
     */
    public $_service;

    public function getService()
    {
        return $this->_service;
    }

    /**
     * @param Attachment $attachment
     * @param array $params
     * @return mixed
     */
    abstract function prepare(Attachment $attachment, $params);

}