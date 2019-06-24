<?php

namespace musan\attachments\controllers;

use musan\attachments\actions\LegacyAddAction;
use musan\attachments\file\UploadedFile;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use musan\attachments\models\Attachment;
use musan\attachments\helpers\Image;

class UploadController extends \yii\web\Controller
{

    public $defaultAction = 'add';
    public $enableCsrfValidation = false;

    public function actions()
    {
        return [
            'add' => LegacyAddAction::class,
        ];
    }

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