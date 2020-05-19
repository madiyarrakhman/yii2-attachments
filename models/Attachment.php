<?php

namespace musan\attachments\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "attachments".
 *
 * The followings are the available columns in table 'attachments':
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $original_name
 * @property string $uid
 * @property string $path
 * @property string $status
 * @property string $type
 * @property string $object
 * @property integer $object_id
 * @property integer $size
 * @property string $attr_name
 * @property string $extension
 * @property boolean $is_downloadable
 * @property boolean $is_watermarked
 */
class Attachment extends \yii\db\ActiveRecord
{

    public const STATUS_CREATED = 0;
    public const STATUS_DELETED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attachment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['original_name', 'uid', 'path'], 'required'],
            [['object_id', 'size', 'status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['original_name', 'uid', 'path', 'object', 'type', 'attr_name', 'extension'], 'string', 'max' => 255],
            [['uid'], 'unique'],
            [['is_downloadable', 'is_watermarked'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('attachment', 'ID'),
            'original_name' => Yii::t('attachment', 'Оригинальное имя файла'),
            'uid' => Yii::t('attachment', 'UID'),
            'path' => Yii::t('attachment', 'Путь к файлу'),
            'status' => Yii::t('attachment', 'Статус'),
            'object' => Yii::t('attachment', 'Объект'),
            'object_id' => Yii::t('attachment', 'ИД объекта'),
            'size' => Yii::t('attachment', 'Размер'),
            'type' => Yii::t('attachment', 'Тип'),
            'attr_name' => Yii::t('attachment', 'Поле'),
            'is_downloadable' => Yii::t('attachment', 'Разрешить скачивание'),
            'is_watermarked' => Yii::t('attachment', 'С водяным знаком'),
            'create_time' => Yii::t('attachment', 'Дата создания'),
            'update_time' => Yii::t('attachment', 'Дата изменения'),
            'extension' => Yii::t('attachment', 'Расширение'),
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                      'status' => self::STATUS_DELETED
                ],
                'replaceRegularDelete' => true,
            ]
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

    public static function find()
    {
        return new AttachmentQuery(get_called_class());
    }
}
