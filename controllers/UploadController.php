<?php

namespace mitrii\attachments\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use mitrii\attachments\components\AttachmentFile;
use mitrii\attachments\models\Attachment;
use mitrii\attachments\helpers\Image;

class UploadController extends \yii\web\Controller
{

    public $defaultAction = 'add';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['delete', 'add'],
                        'allow' => true,
                        'roles' => $this->getModule()->upload_roles,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \common\modules\attachment\Module
     */
    private function getModule()
    {
        return Yii::$app->getModule('attachment');
    }

    public function actionAdd($name = 'filename', $link_width = 640, $link_height = 480)
    {

        Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        $file = AttachmentFile::getInstanceByName($name);
        $file->saveByDeep($this->getModule()->getUploadPath() . '/' , $this->getModule()->getPathDeep());

        $attachment = new Attachment();
        $attachment->original_name = $file->getName();
        $attachment->hash = $file->getHash();
        $attachment->path = $file->getSavePath();
        $attachment->size = $file->getSize();
        $attachment->type = $file->getType();

        if ($attachment->save())
        {
            return [
                'hash' => $attachment->hash,
                'id' => $attachment->id,
                'filename' => $attachment->original_name,
                'filelink' => Image::url($attachment->hash, $link_width, $link_height)
            ];
        } else {
            return ['error'=>$attachment->getErrors()];
        }


    }

    public function actionDelete()
    {
        $hash = Yii::$app->getRequest()->post('hash');

        if (empty($hash)) throw new BadRequestHttpException(400);

        $attachment = Attachment::findOne(['hash'=>$hash]);

        if (empty($attachment)) throw new NotFoundHttpException(404);

        if ($attachment->delete())
        {
            //delete file
        }


    }
}