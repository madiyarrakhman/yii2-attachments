<?php

namespace musan\attachments\migrations;

use yii\db\Migration;

class m160318_065826_attachment_downloadable extends Migration
{
    public function up()
    {
        $this->addColumn('{{%attachment}}', 'is_downloadable', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        $this->dropColumn('{{%attachment}}', 'is_downloadable');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
