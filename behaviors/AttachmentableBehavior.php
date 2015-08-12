<?php

namespace mitrii\attachments\behaviors;

use Yii;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use mitrii\attachments\models\Attachment;

class AttachmentableBehavior extends \yii\base\Behavior
{
    public $required_count = 0;
    public $required_message = false;

    public function init()
    {
        $this->required_message = ($this->required_message) ?: Yii::t('attachments', '{0} minimum required', [$this->required_count]);
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'validate',
        ];
    }

    /**
     * @param $event ModelEvent
     */
    public function validate($event)
    {
        if ($this->required_count > 0)
        {
            $this->owner->addError('attachments', $this->required_message);
            $event->isValid = false;
        }
    }

    public function getMain_photo()
    {
        $object = get_class($this->owner);
        $object_id = $this->owner->id;

        $attachment = Attachment::find()->where(['object' => $object, 'object_id' => $object_id])->orderBy('id')->one();
        return (empty($attachment)) ? null : $attachment->hash;
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
            if ($file->status == 'success' && isset($file->hash))
            {
                $attachment = Attachment::findOne(['hash' => $file->hash]);
                $attachment->updateAttributes(['object'=>$object, 'object_id'=>$object_id]);
            }

        }

    }
} 