<?php
/**
 * Poject: kapital2
 * User: mitrii
 * Date: 5.08.2018
 * Time: 23:05
 * Original File Name: ImageProcessor.php
 */

namespace musan\attachments\components\processors;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use musan\attachments\models\Attachment;
use yii\base\InvalidArgumentException;
use yii\base\InvalidParamException;

class ImageProcessor extends BaseProcessor
{
    public $resizeFilter = ImageInterface::FILTER_MITCHELL;
    public $options = [
        'jpeg_quality' => 95,
        'png_compression_level' => 9
    ];

    /**
     * @param $params
     * @return string Relative to cache folder file path
     */
    public function getCachePath($params)
    {
        $path_params['mode'] = $params['mode'];
        $path_params['width'] = $params['width'];
        $path_params['height'] = $params['height'];
        //$path_params['path'] = $params['path'];

        return $this->service->getCachePath(implode(DIRECTORY_SEPARATOR, $path_params));
    }

    /**
     * @param $image ImageInterface
     * @param $mode string
     * @param $width integer
     * @param $height integer
     * @return ImageInterface
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    public function resizeImage($image, $mode, $width, $height)
    {
        $real_width  = (is_numeric($width)) ? $width :  $image->width;
        $real_height = (is_numeric($height)) ? $height :  $image->height;

        if ($mode === 'c') return $image->copy()->thumbnail(new Box($real_width, $real_height), ImageInterface::THUMBNAIL_OUTBOUND, $this->resizeFilter);
        if ($mode === 'r') return $image->copy()->thumbnail(new Box($real_width, $real_height), ImageInterface::THUMBNAIL_INSET, $this->resizeFilter);

        return $image;
    }

    public function resize($originalFile, $cacheFile, $params)
    {
        if (class_exists('Gmagick', false)) {
            $image = new \Imagine\Gmagick\Imagine();
        }
        elseif (class_exists('Imagick', false)) {
            $image = new \Imagine\Imagick\Imagine();
        }

        if (!isset($params['mode'], $params['width'], $params['height'])) {
            throw new InvalidArgumentException('Not found $params[\'mode\'], $params[\'width\'] or $params[\'height\']');
        }

        $image = $image->open($originalFile);
        $image = $this->resizeImage($image, $params['mode'], $params['width'], $params['height']);

        $image->save($cacheFile, $this->options);
    }

    /**
     * @inheritdoc
     */
    public function prepare(Attachment $attachment, $params)
    {
        $relCacheFilepath = $this->getCachePath($params) . DIRECTORY_SEPARATOR . $attachment->path;
        $cacheFile = $this->service->getPath($relCacheFilepath, true);

        $originalFile = $this->service->getPath($attachment->path);

        if (is_file($cacheFile)) return $relCacheFilepath;

        $this->resize($originalFile, $cacheFile, $params);

        return $relCacheFilepath;
    }

}