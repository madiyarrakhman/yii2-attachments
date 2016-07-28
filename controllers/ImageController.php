<?php

namespace mitrii\attachments\controllers;

use Yii;
use mitrii\attachments\components\RenderManager;
use yii\web\NotFoundHttpException;
use mitrii\attachments\models\Attachment;
use yii\imagine\Image;

class ImageController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['index', 'path'],
                //'lastModified' =>
                'cacheControlHeader' => $this->getModule()->cacheControlHeader,
            ],
        ];
    }

    /**
     * @return \mitrii\attachments\Module
     */
    private function getModule()
    {
        return Yii::$app->getModule('attachment');
    }

    /**
     * @param $attachment Attachment
     * @param $mode string
     * @param $width integer
     * @param $height integer
     */
    public function getCachePath($attachment, $mode, $width, $height, $full)
    {
        return (($full) ? $this->getModule()->getUploadPath() : '') . '/' . 'images_cache' . '/' . $width.'x'.$height.'x'.strtoupper($mode) . '/' . $attachment->path;
    }

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

        $this->renderImage($attachment, $mode, $width, $height);
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



        $this->renderImage($attachment, $mode, $width, $height);
    }

    /**
     * @param Attachment $attachment
     * @param string $mode
     * @param integer $width
     * @param integer $height
     * @throws NotFoundHttpException
     */
    public function renderImage($attachment, $mode, $width, $height)
    {
        header(sprintf('Cache-Control: %s', $this->getModule()->cacheControlHeader));

        if ($this->getModule()->cache_resized) {
            if (file_exists($this->getCachePath($attachment, $mode, $width, $height, true))) {
                Yii::$app->response->xSendFile($this->getCachePath($attachment, $mode, $width, $height, false), null, [
                    'xHeader' => 'X-Accel-Redirect',
                    'mimeType' => $attachment->type,
                    'inline' => true
                ]);
                Yii::$app->end();
            }
        }

        $image = Image::getImagine()->open($this->getModule()->upload_path . '/' . $attachment->path);
        $image = $this->getModule()->get('render')->resizeImage($image, $mode, $width, $height);
        $show_options = $this->getModule()->show_options;
        $image->show(pathinfo($attachment->path, PATHINFO_EXTENSION), $show_options);

        if ($this->getModule()->cache_resized) {
            mkdir(dirname($this->getCachePath($attachment, $mode, $width, $height, true)), 0775, true);
            $image->save($this->getCachePath($attachment, $mode, $width, $height, true), $show_options);
        }


    }
}