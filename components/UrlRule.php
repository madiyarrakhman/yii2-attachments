<?php
/**
 * Poject: kapital2
 * User: mitrii
 * Date: 4.20.2018
 * Time: 16:14
 * Original File Name: UrlRule.php
 */

namespace musan\attachments\components;


use yii\web\UrlRuleInterface;

class UrlRule implements UrlRuleInterface
{
    public $moduleId = 'attachment';

    public $patterns = [

    ];

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        // TODO: Implement createUrl() method.
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
        $params['path'] = $output[5];

        return ['/' . $this->moduleId .'/download/get', $params];
    }
}