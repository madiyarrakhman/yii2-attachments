<?php

namespace mitrii\attachments\behaviors;

use Yii;
use mitrii\attachments\models\Attachment;

class AttachmentableBehavior extends \yii\base\Behavior
{
    public function getAttachments()
    {
        $object = get_class($this->owner);
        $object_id = $this->owner->id;

        return Attachment::findByObject($object, $object_id);
    }

    public function attachAttachments()
    {
        $attachments_data = json_decode(Yii::$app->getRequest()->post('attachments', '[]'));
        $attachments_data = (empty($attachments_data)) ? [] : $attachments_data;


        $object = get_class($this->owner);
        $object_id = $this->owner->id;
        foreach($attachments_data as $file)
        {
            if ($file->status == 'success' && isset($file->hash))
            {
                $attachment = Attachment::findOne(['hash' => $file->hash]);
                $attachment->updateAttributes(['object'=>$object, 'object_id'=>$object_id]);
            }

        }

    }
} 