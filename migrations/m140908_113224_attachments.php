<?php

use yii\db\Schema;
use yii\db\Migration;

class m140908_113224_attachments extends Migration
{
    public function up()
    {
        $this->createTable('attachment', array(
            'id' => Schema::TYPE_PK,
            'original_name' => Schema::TYPE_STRING,
            'hash' => Schema::TYPE_STRING,
            'path' => Schema::TYPE_STRING,
            'object' => Schema::TYPE_STRING,
            'object_id' => Schema::TYPE_INTEGER,
            'size' => Schema::TYPE_INTEGER,
            'type' => Schema::TYPE_STRING,

            'create_time' => Schema::TYPE_TIMESTAMP,
            'update_time' => Schema::TYPE_TIMESTAMP,
        ));

        $this->createIndex('attachment_hash', 'attachment', 'hash', true);
        $this->createIndex('attachment_model', 'attachment', 'object, object_id');
    }

    public function down()
    {
        $this->dropTable('attachment');
    }
}