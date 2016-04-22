<?php

namespace mitrii\attachments\behaviors;

use Yii;
use yii\db\ActiveRecord;
use mitrii\attachments\models\Attachment;

class AttributesAttachmentBehavior extends \yii\base\Behavior
{


    public $attributes = [];


    public $upload_from_url = false;

    public function getModule()
    {
        return Yii::$app->getModule('attachments');
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'asterSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'asterSave',
        ];
    }

    public function asterSave($event)
    {

        foreach($this->attributes as $attribute)
        {
            if ($this->upload_from_url) $this->owner->$attribute = $this->uploadFromUrl($this->owner->$attribute);

            $value = $this->owner->getAttribute($attribute);

            if (!empty($value))
            {
                $attachment = Attachment::findOne(['hash'=>$value]);
                if (!empty($attachment))
                {
                    $attachment->updateAttributes([
                        'object' => get_class($this->owner),
                        'object_id' => $this->owner->id,
                        'attr_name' => $attribute,
                    ]);
                }
            }
        }
    }

    public function uploadFromUrl($value)
    {
        if (!(new \yii\validators\UrlValidator())->validate($value, $error)) return $value;

        $file = new \mitrii\attachments\components\AttachmentFile();
        $filename = $file->generateSavePath($this->getModule()->getUploadPath() . '/' , $this->getModule()->getPathDeep(), 'jpg');

        copy($value, $filename);

        $attachment = new Attachment();
        $attachment->original_name = basename($value);
        $attachment->hash = $file->getHash();
        $attachment->path = $file->getSavePath();

        $attachment->type = \yii\helpers\FileHelper::getMimeType($filename);
        $attachment->size = filesize($filename);

        if ($attachment->save())
        {
            return $attachment->hash;
        } else {
            return '';
        }
    }
} 