<?php
namespace musan\attachments\actions;

use Yii;
use musan\attachments\file\UploadedFile;
use musan\attachments\helpers\Url;
use musan\attachments\models\Attachment;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\web\Response;

class UploadAction extends Action
{

    /**
     * @return \musan\attachments\Module
     */
    private function getModule()
    {
        return Yii::$app->getModule('attachment');
    }

    public function init()
    {
        parent::init();

        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
    }

    /**
     * @param string $name
     * @param int $link_width
     * @param int $link_height
     * @param bool $arrayResponse
     * @throws \Exception
     * @return Attachment
     */
    public function upload($name = 'filename')
    {
        $file = UploadedFile::getInstanceByName($name);
        $file->upload_dir = $this->getModule()->getUploadPath() . '/';
        $file->path_deep = $this->getModule()->getPathDeep();
        $file->save();

        /**
         * @var $attachment ActiveRecord
         */
        $attachment = $this->getModule()->get('service')->createAttachment($file);

        if ($attachment->save())
        {
            return $attachment;
        } else {
            throw new \Exception('File not saved. ' . $attachment->getErrorSummary());
        }
    }

    public function run($name = 'filename')
    {

        try {
            $attachment = $this->upload($name);

            $result = [
                'uid' => $attachment->uid,
                'filename' => $attachment->original_name,
                'filelink' => Url::toFile($attachment->uid),
                'type' => $attachment->type,
                'size' => $attachment->size,
            ];

            return $result;

        } catch (\Exception $e) {
             return ['result' => false, 'error' => $e->getMessage()];
        }
    }
}