<?php

namespace musan\attachments\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use musan\attachments\components\AttachmentFile;
use musan\attachments\models\Attachment;
use musan\attachments\helpers\Image;

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
     * @return \musan\attachments\Module
     */
    private function getModule()
    {
        return Yii::$app->getModule('attachment');
    }

    public function actionAdd($name = 'filename', $link_width = 640, $link_height = 480, $arrayResponse = false)
    {

        Yii::$app->getResponse()->format = Response::FORMAT_JSON;

        $file = AttachmentFile::getInstanceByName($name);
        $file->saveByDeep($this->getModule()->getUploadPath() . '/' , $this->getModule()->getPathDeep());

        $attachment = new Attachment();
        $attachment->original_name = $file->getName();
        $attachment->uid = $file->getUid();
        $attachment->path = $file->getSavePath();
        $attachment->size = $file->getSize();
        $attachment->type = $file->getType();
        $attachment->extension = $file->getExtensionName();

        if ($attachment->save())
        {
            $result = [
                'uid' => $attachment->uid,
                'id' => $attachment->id,
                'filename' => $attachment->original_name,
                'name' => $attachment->original_name,
                'filelink' => Image::url($attachment->uid, $link_width, $link_height),
                'url' => Image::url($attachment->uid, $link_width, $link_height),
                "thumbnail_url" => Image::url($attachment->uid, $link_width, $link_height),
                "type" => $attachment->type,
                "size" => $attachment->size,
            ];

            return ($arrayResponse) ? ['files' => [$result]] : $result;
        } else {
            return ['error'=>$attachment->getErrors()];
        }


    }
    
    public function actionDelete()
    {
        $uid = Yii::$app->getRequest()->post('uid');

        if (empty($uid)) throw new BadRequestHttpException(400);

        $attachment = Attachment::findOne(['uid'=>$uid]);

        if (empty($attachment)) throw new NotFoundHttpException(404);

        if ($attachment->delete())
        {
            //delete file
        }


    }
}