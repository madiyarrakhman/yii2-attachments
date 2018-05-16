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

    public function actionGet($key, $path)
    {
        $pathinfo = pathinfo($path);
        $hash = $pathinfo['filename'];

        // todo: check key
        $query_string = Yii::$app->request->getQueryString();

        //try {
        $response = $this->getAttachmentService()->send($hash, Yii::$app->request->getQueryParams());
        //} catch (\Exception $e) {
        //    throw new \yii\web\NotFoundHttpException();
        //}

        return $response;
    }
}