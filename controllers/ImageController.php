<?php

namespace musan\attachments\controllers;

use Imagine\Image\ImageInterface;
use Yii;
use musan\attachments\components\RenderManager;
use yii\web\NotFoundHttpException;
use musan\attachments\models\Attachment;

/**
 * Class ImageController
 * @package musan\attachments\controllers
 * @deprecated
 */
class ImageController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => \yii\filters\HttpCache::class,
                'only' => ['index', 'path'],
                //'lastModified' =>
                'cacheControlHeader' => $this->getModule()->cacheControlHeader,
            ],

        ];
    }

    /**
     * @return \musan\attachments\Module
     */
    private function getModule()
    {
        return $this->module;
    }

    /**
     * @return \musan\attachments\components\AttachmentService
     */
    private function getAttachmentService()
    {
        return $this->module->get('service');
    }

    /**
     * @param $attachment Attachment
     * @param $mode string
     * @param $width integer
     * @param $height integer
     * @return string
     */
    public function getCachePath($attachment, $mode, $width, $height, $filter, $full)
    {
        return (($full) ? $this->getModule()->getUploadPath() : '') .
        '/' . 'images_cache' .
        '/' . $width.'x'.$height.'x'.strtoupper($mode) .
        (($filter != (ImageInterface::FILTER_UNDEFINED)) ? '/'.$filter : '') .
        '/' . $attachment->path;
    }

    /**
     * @param $key
     * @param $mode
     * @param $width
     * @param $height
     * @throws NotFoundHttpException
     * @deprecated Use action Get
     */
    public function actionIndex($key, $mode, $width, $height)
    {

        $path[] = Yii::$app->getRequest()->get('w1');
        $path[] = Yii::$app->getRequest()->get('w2');
        $path[] = Yii::$app->getRequest()->get('w3');
        $path[] = Yii::$app->getRequest()->get('w4');
        $path[] = Yii::$app->getRequest()->get('w5');
        $path = array_filter($path, function($value) {
            return ($value !== null && $value !== false && $value !== '');
        });

        $image_path = sprintf('%s/%s', implode('/',$path), Yii::$app->getRequest()->get('name'));

        $hash = pathinfo(str_replace('/','',$image_path), PATHINFO_FILENAME);

        $attachment = Attachment::find()->where(['hash' => $hash])->one();

        if ($key !== md5(sprintf('%s/%s/%s', $width, $height, $attachment->path).$this->getModule()->get('render')->secret)) throw new NotFoundHttpException();
        if (!file_exists(realpath($this->getModule()->upload_path.'/'.$attachment->path)))
        {
            Yii::error('Attachment not found: ' . realpath($this->getModule()->upload_path.'/'.$attachment->path));
            throw new NotFoundHttpException();
        }

        $filter = Yii::$app->request->get('filter', $this->getModule()->image_filter);

        $this->renderImage($attachment, $mode, $width, $height, $filter);
    }

    public function actionPath($key, $mode, $width, $height)
    {

        $path[] = Yii::$app->getRequest()->get('w1');
        $path[] = Yii::$app->getRequest()->get('w2');
        $path[] = Yii::$app->getRequest()->get('w3');
        $path = array_filter($path);

        $image_path = sprintf('%s/%s', implode('/',$path), Yii::$app->getRequest()->get('name'));

        $attachment = Attachment::find()->where(['path' => $image_path])->one();

        if ($key !== md5(sprintf('%s/%s/%s', $width, $height, $attachment->path).$this->getModule()->get('render')->secret)) throw new NotFoundHttpException();
        if (!file_exists(realpath($this->getModule()->upload_path.'/'.$attachment->path)))
        {
            Yii::error('Attachment not found: ' . realpath($this->getModule()->upload_path.'/'.$attachment->path));
            throw new NotFoundHttpException();
        }

        $filter = Yii::$app->request->get('filter', $this->getModule()->image_filter);

        $this->renderImage($attachment, $mode, $width, $height, $filter);
    }

    /**
     * @param Attachment $attachment
     * @param string $mode
     * @param integer $width
     * @param integer $height
     * @param string $filter
     * @throws NotFoundHttpException
     */
    public function renderImage($attachment, $mode, $width, $height, $filter)
    {
        header(sprintf('Cache-Control: %s', $this->getModule()->cacheControlHeader));

        if ($this->getModule()->cache_resized) {
            if (file_exists($this->getCachePath($attachment, $mode, $width, $height, $filter, true))) {
                Yii::$app->response->xSendFile($this->getCachePath($attachment, $mode, $width, $height, $filter, false), null, [
                    'xHeader' => 'X-Accel-Redirect',
                    'mimeType' => $attachment->type,
                    'inline' => true
                ]);
                Yii::$app->end();
            }
        }

        if (class_exists('Gmagick', false)) {
            $image = new \Imagine\Gmagick\Imagine();
        }
        elseif (class_exists('Imagick', false)) {
            $image = new \Imagine\Imagick\Imagine();
        }

        $image = $image->open($this->getModule()->upload_path . '/' . $attachment->path);
        $image = $this->getModule()->get('render')->resizeImage($image, $mode, $width, $height, $filter);
        $show_options = $this->getModule()->show_options;
        $image->show(pathinfo($attachment->path, PATHINFO_EXTENSION), $show_options);

        if ($this->getModule()->cache_resized) {
            @mkdir(dirname($this->getCachePath($attachment, $mode, $width, $height, $filter, true)), 0775, true);
            $image->save($this->getCachePath($attachment, $mode, $width, $height, $filter, true), $show_options);
        }


    }
}