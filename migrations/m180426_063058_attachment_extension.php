<?php
namespace musan\attachments\migrations;

use yii\db\Migration;

/**
 * Class m180426_063057_attachment_extension
 */
class m180426_063058_attachment_extension extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%attachment}}', 'update_time', $this->timestamp()->null());
        $this->addColumn('{{%attachment}}', 'extension', $this->string(10)->null()->after('type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%attachment}}', 'extension');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180426_063057_attachment_extension cannot be reverted.\n";

        return false;
    }
    */
}
