<?php

namespace mitrii\attachments\behaviors;

use Yii;
use yii\db\ActiveRecord;
use mitrii\attachments\models\Attachment;

class AttributesAttachmentBehavior extends \yii\base\Behavior
{
    public $attributes = [];

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
} 