<?php
namespace musan\attachments\migrations;

use yii\db\Migration;

/**
 * Class m180520_095904_attachment_uid
 */
class m180520_095904_attachment_uid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%attachment}}', 'hash', 'uid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%attachment}}', 'uid', 'hash');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180520_095904_attachment_uid cannot be reverted.\n";

        return false;
    }
    */
}
