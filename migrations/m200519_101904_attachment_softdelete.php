<?php
namespace musan\attachments\migrations;

use yii\db\Migration;

/**
 * Class m180520_095904_attachment_uid
 */
class m200519_101904_attachment_softdelete extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%attachment}}', 'status', $this->integer()
            ->defaultValue(0)
            ->after('path')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%attachment}}', 'status');
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
