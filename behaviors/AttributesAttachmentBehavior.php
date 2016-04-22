<?php

namespace mitrii\attachments\behaviors;

use common\models\Block;
use Yii;
use yii\db\ActiveRecord;
use mitrii\attachments\models\Attachment;

class AttributesAttachmentBehavior extends \yii\base\Behavior
{

    public $attributes = [];


    public $upload_from_url = false;

    public $when_upload_from_url;

    public function getModule()
    {
        return Yii::$app->getModule('attachment');
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
            if ($this->upload_from_url && ($this->when_upload_from_url === null || call_user_func($this->when_upload_from_url, $this->owner, $attribute)))
            {
                $this->owner->$attribute = $this->uploadFromUrl($this->owner->$attribute);
                $this->owner->updateAll([$attribute => $this->owner->$attribute], ['id' => $this->owner->id]);
            }

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
        $tmp = explode(".", $value);
        $file_ext = array_pop($tmp);
        $filename = $this->getModule()->getUploadPath() . '/' . $file->generateSavePath($this->getModule()->getUploadPath() . '/' , $this->getModule()->getPathDeep(), $file_ext);

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