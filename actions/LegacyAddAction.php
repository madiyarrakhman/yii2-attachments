<?php
namespace musan\attachments\actions;

use musan\attachments\helpers\Url;

class LegacyAddAction extends UploadAction
{
    public function run($name = 'filename')
    {
        $link_width = \Yii::$app->getRequest()->get('link_width', 640);
        $link_height = \Yii::$app->getRequest()->get('link_height', 480);
        $arrayResponse = \Yii::$app->getRequest()->get('arrayResponse', false);

        try {
            $attachment = $this->upload($name);

            $result = [
                'uid' => $attachment->uid,
                'id' => $attachment->id,
                'filename' => $attachment->original_name,
                'name' => $attachment->original_name,
                'filelink' => Url::toImage($attachment->uid, $link_width, $link_height),
                'url' => Url::toImage($attachment->uid, $link_width, $link_height),
                'thumbnail_url' => Url::toImage($attachment->uid, $link_width, $link_height),
                'type' => $attachment->type,
                'size' => $attachment->size,
            ];

            return ($arrayResponse) ? ['files' => [$result]] : $result;

        } catch (Exception $e) {
            return ['result' => false, 'error' => $e->getMessage()];
        }
    }
}