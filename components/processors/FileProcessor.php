<?php
/**
 * Poject: kapital2
 * User: mitrii
 * Date: 5.08.2018
 * Time: 23:05
 * Original File Name: ImageProcessor.php
 */

namespace musan\attachments\components\processors;

use musan\attachments\models\Attachment;
use yii\base\BaseObject;

class FileProcessor extends BaseProcessor
{
    /**
     * @param Attachment $attachment
     * @param array $params
     * @return mixed
     */
    public function prepare(Attachment $attachment, $params)
    {
        return $attachment->path;
    }
}