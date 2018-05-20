<?php
namespace musan\attachments\components;

use musan\attachments\components\processors\BaseProcessor;
use musan\attachments\components\processors\FileBaseProcessor;
use musan\attachments\components\processors\ImageProcessor;
use musan\attachments\models\Attachment;
use musan\attachments\Module;
use Yii;
use yii\base\Component;
use yii\base\InvalidValueException;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Class AttachmentService
 * @property-read Module $module
 * @package musan\attachments\components
 */
class AttachmentService extends Component
{

    public $pathPermissions = 0775;

    public $_module;

    private $_collection = [];

    /**
     * @var null|BaseProcessor[]
     */
    private $_processors;

    /**
     * @var array
     */
    public $processors = [];

    /**
     * @var array
     */
    public $defaultProcessors = [
        'image' => [
            'class' => ImageProcessor::class,
            'extensions' => ['jpg', 'png', 'bmp', 'gif', 'jpeg'],
        ],
        'file' => [
            'class' => FileBaseProcessor::class,
            'extensions' => [],
        ],
    ];

    public function init()
    {
        $this->processors = ArrayHelper::merge($this->defaultProcessors, $this->processors);
    }

    public function getModule()
    {
        return $this->_module;
    }

    /**
     * @param string $id
     * @throws \yii\base\InvalidConfigException
     */
    public function getProcessor($id = 'file')
    {
        if (!isset($this->_processors[$id])) {
            $this->_processors[$id] = Yii::createObject($this->processors[$id]);
            $this->_processors[$id]->_service = $this;
        }

        return $this->_processors[$id];
    }

    /**
     * @param $uid string
     * @return Attachment|null
     */
    public function getAttachment($uid)
    {
        if (!isset($this->_collection[$uid])) {
            $this->_collection[$uid] = Attachment::find()->whereUID($uid)->one();
        }

        return $this->_collection[$uid];
    }

    /**
     * @param Attachment $attachment
     * @return BaseProcessor
     * @throws \yii\base\InvalidConfigException
     */
    public function detectProcessor($attachment)
    {
        foreach ($this->processors as $id => $processor)
        {
            if (in_array($attachment->extension, $processor['extensions'])) {
                return $this->getProcessor($id);
            }
        }

        return $this->getProcessor('file');
    }

    public function getCachePath($relativePath)
    {
        return 'cache' . DIRECTORY_SEPARATOR . $relativePath;
    }

    /**
     * @param string $relativePath Return full path to attachment file
     * @param bool $create Create path folders if not exists
     * @return string
     */
    public function getPath($relativePath, $create = false)
    {
        $result = $this->module->upload_path . DIRECTORY_SEPARATOR . $relativePath;

        if ($create) {
            @mkdir(dirname($result), $this->pathPermissions, true);
        }

        return $result;
    }


    /**
     * @param string $uid
     * @param array $params
     * @return Response
     * @throws \yii\base\InvalidValueException
     * @throws \yii\base\InvalidConfigException
     */
    public function send($uid, $params)
    {
        $attachment = $this->getAttachment($uid);

        if (null === $attachment) throw new InvalidValueException('Attachment not found');

        $processor = $this->detectProcessor($attachment);

        $filePath = $processor->prepare($attachment, $params);

        return $this->sendFile($attachment, $this->getPath($filePath));
    }

    /**
     * @param Attachment $attachment
     * @param $fullPath
     * @return Response
     */
    public function sendFile(Attachment $attachment, $fullPath)
    {
        return Yii::$app->response->sendFile($fullPath, $attachment->original_name, [
            'mimeType' => $attachment->type,
            'inline' => true,
        ]);
    }
}