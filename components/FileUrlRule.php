<?php
/**
 * Poject: kapital2
 * User: mitrii
 * Date: 10.08.2018
 * Time: 12:24 PM
 * Original File Name: ImageUrlRule.php
 */

namespace musan\attachments\components;


class FileUrlRule extends UrlRule
{
    const URL_FORMAT = '/filename';

    public function createUrl($manager, $route, $params)
    {

        if ($route === $this->getModule()->id . '/download/file') {

            $url = \Yii::$app->cache->get(md5($route . serialize($params)));

            if ($url === false) {
                $attachment = $this->getModule()->get('service')->getAttachment($params['uid']);

                if ($attachment === null) {
                    return false;
                }

                $data = [
                    'filename' => $params['uid'] . '.' . $attachment->extension
                ];

                $url_without_key = $this->fillUrlString($data);

                $url = '/' . $this->generateKey($url_without_key) . $url_without_key;

                \Yii::$app->cache->set(md5($route . serialize($params)), $url, 2628000);
            }

            return $url;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        preg_match("/(.{32})\/(.*)/", $request->getPathInfo(), $output);

        if (empty($output)) {
            return false;
        }

        $params['key']  = $output[1];
        $params['filename'] = $output[5];

        if ($this->checkKey($params) === false) {
            return false;
        }

        return ['/' . $this->getModule()->id .'/download/index', $params];
    }
}