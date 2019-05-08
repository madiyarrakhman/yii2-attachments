<?php
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
    public $_moduleID;

    public $patterns = [

    ];

    const URL_FORMAT = '/mode/width/height/filename';

    /**
     * @return Module
     */
    public function getModule()
    {
        return \Yii::$app->getModule($this->_moduleID);
    }

    /**
     * @param $format string
     * @param $params array
     * @return string
     */
    public function fillUrlString($params, $format = null)
    {
        $format = $format ?? $this::URL_FORMAT;
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
        return false;
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        return false;
    }
}