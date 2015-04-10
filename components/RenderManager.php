<?php

namespace mitrii\attachments\components;

use Yii;
use mitrii\attachments\models\Attachment;

class RenderManager extends \yii\base\Component
{

    /**
     * @var string Host for images url
     */
    public $image_url_host;

    /**
     * @var string Template for image placeholder url
     */
    public $image_placeholder_template = 'http://placehold.it/{widht}x{height}/{bg_color}/{text_color}/&text={text}';

    /**
     * @var string Image default resize mode. 'c' - crop, 'r' - resize
     */
    public $image_resize_mode = 'c';

    /**
     * @var string Secret string for nginx image resizer
     */
    public $secret;

    /**
     * @return \common\modules\attachment\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('attachment');
    }


    protected function getPlaceholder($hash, $width, $height)
    {
        return str_replace(
            array(
                '{widht}',
                '{height}',
                '{bg_color}',
                '{text_color}',
                '{text}'
            ),
            array(
                $width,
                $height,
                substr(md5($hash), 0, 6),
                '000000',
                substr($hash, 0, 2).' '.$width.'x'.$height,
            ),
            $this->image_placeholder_template
        );
    }

    /**
     * @param $hash
     * @return Attachment
     */
    public function getAttachment($hash)
    {
        return Attachment::findOne(['hash' => $hash]);
    }

    /**
     * @param string $hash Attachment hash value
     * @param int $width Image width
     * @param int $height Image haight
     * @param string $mode Image resize mode
     * @return string Url to image
     */
    public function getImageUrl($hash, $width, $height, $mode = null)
    {
        if (YII_DEBUG) return $this->getPlaceholder($hash, $width, $height);

        $url = Yii::$app->getCache()->get($hash.'.'.$width.'.'.$height.'.'.$mode);
        if ($url !== false) return $url;


        $mode = empty($mode) ? $this->image_resize_mode : $mode;

        $attachment = $this->getAttachment($hash);

        if (empty($attachment))
        {
            Yii::warning(sprintf('Attachment not found. Hash: %s', $hash));
            return '';
        }

        $key = md5($attachment->path.$width.$height.$this->secret);

        $url = sprintf('%s/%s/%s/%s/%s/%s', $this->image_url_host, $mode,  $key, $width, $height, $attachment->path);
        Yii::$app->getCache()->set($hash.'.'.$width.'.'.$height.'.'.$mode, $url);

        return $url;
    }

} 