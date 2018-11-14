<?php

namespace musan\attachments\behaviors;

use common\models\Block;
use musan\attachments\file\RemoteFile;
use Yii;
use yii\db\ActiveRecord;
use musan\attachments\models\Attachment;

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
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
        ];
    }

    public function afterSave($event)
    {

        foreach($this->attributes as $attribute)
        {

            $this->loadByUrl($attribute);

            $value = $this->owner->getAttribute($attribute);

            if (!empty($value))
            {
                $attachment = Attachment::findOne(['uid'=>$value]);
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

    public function loadByUrl($attribute)
    {
        if ($this->upload_from_url && ($this->when_upload_from_url === null || call_user_func($this->when_upload_from_url, $this->owner, $attribute)))
        {
            $this->owner->$attribute = $this->uploadFromUrl($this->owner->$attribute);
            $this->owner->updateAll([$attribute => $this->owner->$attribute], ['id' => $this->owner->id]);
        }
    }

    public function uploadFromUrl($value)
    {
        if (!(new \yii\validators\UrlValidator())->validate($value, $error)) return $value;

        $file = new RemoteFile([
            'upload_dir' => $this->getModule()->getUploadPath() . '/',
            'path_deep' => $this->getModule()->getPathDeep(),
            'url' => $value
        ]);
        $file->save();

        $attachment = $this->getModule()->get('service')->createAttachment($file);

        if ($attachment->save())
        {
            return $attachment->uid;
        } else {
            return '';
        }
    }
} 