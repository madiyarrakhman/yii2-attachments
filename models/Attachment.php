<?php

namespace musan\attachments\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "attachments".
 *
 * The followings are the available columns in table 'attachments':
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $original_name
 * @property string $hash
 * @property string $path
 * @property string $type
 * @property string $object
 * @property integer $object_id
 * @property integer $size
 * @property string $attr_name
 * @property boolean $is_downloadable
 */
class Attachment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['original_name', 'hash', 'path'], 'required'],
            [['object_id', 'size'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['original_name', 'hash', 'path', 'object', 'type', 'attr_name'], 'string', 'max' => 255],
            [['hash'], 'unique'],
            [['is_downloadable'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'original_name' => Yii::t('app', 'Оригинальное имя файла'),
            'hash' => Yii::t('app', 'Хеш имени файла'),
            'path' => Yii::t('app', 'Путь к файлу'),
            'object' => Yii::t('app', 'Объект'),
            'object_id' => Yii::t('app', 'ИД объекта'),
            'size' => Yii::t('app', 'Размер'),
            'type' => Yii::t('app', 'Тип'),
            'attr_name' => Yii::t('app', 'Поле'),
            'is_downloadable' => Yii::t('app', 'Разрешить скачивание'),
            'create_time' => Yii::t('app', 'Дата создания'),
            'update_time' => Yii::t('app', 'Дата изменения'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @param $object
     * @param $object_id
     * @return Attachment[] Attachments for model object
     */
    static function findByObject($object, $object_id)
    {
        return self::find()->where('object = :object AND object_id = :object_id', ['object' => $object, 'object_id' => $object_id])->andWhere('attr_name is null')->all();
    }


    public function getIsImage()
    {
        return strpos($this->type, 'image') === 0;
    }

    public function getIsAudio()
    {
        return strpos($this->type, 'audio') === 0;
    }
}
