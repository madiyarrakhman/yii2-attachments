<?php
namespace musan\attachments\components;


class ImageUrlRule extends UrlRule
{
    const URL_FORMAT = '/mode/width/height/filename';

    public $placeholderEnable = false;
    public $placeholderTemplate = '#';

    protected function generatePlaceholder($uid, $width, $height)
    {
        return str_replace(
            array(
                '{widht}',
                '{height}',
                '{bg_color}',
                '{text_color}',
                '{text}'
            ),
            array(
                $width,
                $height,
                substr(md5($uid), 0, 6),
                '000000',
                substr($uid, 0, 2).' '.$width.'x'.$height,
            ),
            $this->placeholderTemplate
        );
    }

    public function createUrl($manager, $route, $params)
    {

        if ($route === $this->getModule()->id . '/download/image') {

            // return url to placeholder if enabled
            if ($this->placeholderEnable) {
                return $this->generatePlaceholder($params['uid'], $params['width'], $params['height']);
            }


            $url = \Yii::$app->cache->get(md5($route . serialize($params)));

            if ($url === false) {
                $attachment = $this->getModule()->get('service')->getAttachment($params['uid']);

                if ($attachment === null) {
                    return false;
                }

                $data = [
                    'mode' => $params['mode'] ?? $this->getModule()->image_default_mode,
                    'width' => $params['width'],
                    'height' => $params['height'],
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
        preg_match("/(.{32})\/([cr])\/(\d+)\/(\d+)\/(.*)/", $request->getPathInfo(), $output);

        if (empty($output)) {
            return false;
        }

        $params['key']  = $output[1];
        $params['mode'] = $output[2];
        $params['width'] = $output[3];
        $params['height'] = $output[4];
        $params['filename'] = $output[5];

        if ($this->checkKey($params) === false) {
            return false;
        }

        return ['/' . $this->getModule()->id .'/download/index', $params];
    }
}