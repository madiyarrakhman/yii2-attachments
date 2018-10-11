<?php
namespace musan\attachments\helpers;

use musan\attachments\Module;
use yii\base\InvalidConfigException;
use yii\helpers\Url as YiiUrl;

class Url
{
    /**
     * @throws InvalidConfigException
     */
    private static function getModule()
    {
        $module = Module::getInstance();

        if ($module === null) {
            throw new InvalidConfigException('Module ' . Module::class . 'must be bootstrapped');
        }

        return $module;
    }

    private static function getRoute($type = 'file')
    {
        $module = self::getModule();
        return '/' . $module->id . '/download/' . $type;
    }

    public static function toFile($uid)
    {
        return YiiUrl::to([self::getRoute('file'), 'uid' => $uid]);
    }

    public static function toImage($uid, $width, $height, $mode = null)
    {
        return YiiUrl::to([self::getRoute('image'), 'uid' => $uid, 'mode' => $mode, 'width' => $width, 'height' => $height]);
    }
}