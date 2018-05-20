<?php

namespace musan\attachments\behaviors;

use Yii;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use musan\attachments\models\Attachment;

class AttachmentableBehavior extends \yii\base\Behavior
{

    public function getMain_photo()
    {
        $object = get_class($this->owner);
        $object_id = $this->owner->id;

        $attachment = Attachment::find()->where(['object' => $object, 'object_id' => $object_id])->orderBy('id')->one();
        return (empty($attachment)) ? null : $attachment->uid;
    }

    public function getAttachments()
    {
        $object = get_class($this->owner);
        $object_id = $this->owner->id;

        return Attachment::findByObject($object, $object_id);
    }

    public function attachAttachments($param_name = 'attachments')
    {
        $attachments_data = json_decode(Yii::$app->getRequest()->post($param_name, '[]'));
        $attachments_data = (empty($attachments_data)) ? [] : $attachments_data;


        $object = get_class($this->owner);
        $object_id = $this->owner->id;
        foreach($attachments_data as $file)
        {
            if ($file->status == 'success' && isset($file->uid))
            {
                $attachment = Attachment::findOne(['uid' => $file->uid]);
                $attachment->updateAttributes(['object'=>$object, 'object_id'=>$object_id]);
            }

        }

    }
} 