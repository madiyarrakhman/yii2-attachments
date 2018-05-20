<?php
/**
 * Poject: kapital2
 * User: mitrii
 * Date: 4.20.2018
 * Time: 16:14
 * Original File Name: UrlRule.php
 */

namespace musan\attachments\components;


use musan\attachments\Module;
use yii\web\Request;
use yii\web\UrlRuleInterface;

/**
 * Class UrlRule
 * @package musan\attachments\components
 */
class UrlRule implements UrlRuleInterface
{
    public $_module;

    public $patterns = [

    ];

    const URL_FORMAT = '/mode/width/height/filename';

    /**
     * @return Module
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * @param $format string
     * @param $params array
     * @return string
     */
    public function fillUrlString($params, $format = self::URL_FORMAT)
    {
        return str_replace(array_keys($params), array_values($params), $format);
    }

    /**
     * @param $string
     * @return string
     */
    public function generateKey($string)
    {
        $secret = $this->getModule()->secret;

        return md5($secret . $string);
    }

    /**
     * @param $data array
     * @return bool
     */
    public function checkKey($data)
    {
        $url = $this->fillUrlString($data);
        return $this->generateKey($url) === $data['key'];
    }

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {

        if ($route === $this->getModule()->id . '/download/index') {

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