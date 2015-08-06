<?php

namespace mitrii\attachments\controllers;

use Yii;
use mitrii\attachments\components\RenderManager;
use yii\web\NotFoundHttpException;
use mitrii\attachments\models\Attachment;
use yii\imagine\Image;

class ImageController extends \yii\web\Controller
{
    /**
     * @return \mitrii\attachments\Module
     */
    private function getModule()
    {
        return Yii::$app->getModule('attachment');
    }

    public function actionIndex($key, $mode, $width, $height)
    {
        $w1 = Yii::$app->getRequest()->get('w1');
        $w2 = Yii::$app->getRequest()->get('w2');
        $w3 = Yii::$app->getRequest()->get('w3');
        $w4 = Yii::$app->getRequest()->get('w4');
        $w5 = Yii::$app->getRequest()->get('w5');

        $image_path = sprintf('%s/%s/%s/%s/%s/%s', $w1, $w2, $w3, $w4, $w5, Yii::$app->getRequest()->get('name'));
        $hash = pathinfo(str_replace('/','',$image_path), PATHINFO_FILENAME);

        $attachment = Attachment::find()->where(['hash' => $hash])->one();

        if ($key !== md5(sprintf('%s/%s/%s', $width, $height, $attachment->path).$this->getModule()->get('render')->secret)) throw new NotFoundHttpException();
        if (!file_exists(realpath($this->getModule()->upload_path.'/'.$attachment->path))) throw new NotFoundHttpException();


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
        $image = Image::getImagine()->open($this->getModule()->upload_path . '/' . $attachment->path);

        $image = $this->getModule()->get('render')->resizeImage($image, $mode, $width, $height);

        $image->show($attachment->type);
    }
}