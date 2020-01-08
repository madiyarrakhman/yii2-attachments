<?php
/**
 * Poject: kapital2
 * User: mitrii
 * Date: 5.14.2018
 * Time: 22:15
 * Original File Name: DownloadController.php
 */

namespace musan\attachments\controllers;

use Yii;
use yii\web\Controller;

class DownloadController extends Controller
{

    public function behaviors()
    {
        return [
            [
                'class' => \yii\filters\HttpCache::class,
                'only' => ['index'],
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

    public function actionIndex($key, $filename)
    {
        $pathinfo = pathinfo($filename);
        $uid = $pathinfo['filename'];

        //try {
        $response = $this->getAttachmentService()->send($uid, Yii::$app->request->getQueryParams());
        //} catch (\Exception $e) {
        //    throw new \yii\web\NotFoundHttpException();
        //}

        return $response;
    }
}