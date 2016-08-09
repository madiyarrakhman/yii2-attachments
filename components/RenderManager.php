<?php

namespace mitrii\attachments\components;

use Imagine\Image\ImageInterface;
use Yii;
use mitrii\attachments\models\Attachment;
use yii\imagine\Image;
use Imagine\Image\Box;
use Imagine\Image\Point;

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

    public $placeholder_on_debug = true;

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
        if ($this->placeholder_on_debug && YII_DEBUG) return $this->getPlaceholder($hash, $width, $height);

        $full_url = Yii::$app->getCache()->get($hash.'.'.$width.'.'.$height.'.'.$mode);
        if ($full_url !== false) return $full_url;


        $mode = empty($mode) ? $this->image_resize_mode : $mode;

        $attachment = $this->getAttachment($hash);

        if (empty($attachment))
        {
            Yii::warning(sprintf('Attachment not found. Hash: %s', $hash));
            return '';
        }

        $url = sprintf('%s/%s/%s', $width, $height, $attachment->path);
        $key = md5($url.$this->secret);

        $full_url = sprintf('%s/%s/%s/%s', $this->image_url_host, $mode,  $key, $url);

        Yii::$app->getCache()->set($hash.'.'.$width.'.'.$height.'.'.$mode, $full_url);

        return $full_url;
    }

    /**
     * @param $image ImageInterface
     * @param $mode string
     * @param $width integer
     * @param $height integer
     * @return ImageInterface
     */
    public function resizeImage($image, $mode, $width, $height, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $real_width  = (is_numeric($width)) ? $width :  $image->width;
        $real_height = (is_numeric($height)) ? $height :  $image->height;

        //$box = new Box($real_width, $real_height);
        //if


        if ($mode == 'c') return $image->copy()->resize(new Box($real_width, $real_height), $filter)->crop(new Point(0, 0), new Box($real_width, $real_height));
        if ($mode == 'r') return $image->copy()->resize(new Box($real_width, $real_height), $filter);

        //if ($mode == 'c') return $image->copy()->thumbnail(new Box($real_width, $real_height), ImageInterface::THUMBNAIL_OUTBOUND, $filter);
        //if ($mode == 'r') return $image->copy()->thumbnail(new Box($real_width, $real_height), ImageInterface::THUMBNAIL_INSET, $filter);

        return $image;
    }


    public function getAudioUrl($hash)
    {
        $full_url = Yii::$app->getCache()->get($hash);
        if ($full_url !== false) return $full_url;

        $attachment = $this->getAttachment($hash);

        if (empty($attachment))
        {
            Yii::warning(sprintf('Attachment not found. Hash: %s', $hash));
            return '';
        }

        $url = $attachment->path;
        $key = md5($url.$this->secret);

        $full_url = sprintf('%s/%s/%s/%s', $this->image_url_host, 'audio', $key, $url);

        Yii::$app->getCache()->set($hash, $full_url);

        return $full_url;
    }
} 