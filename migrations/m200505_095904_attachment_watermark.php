<?php
namespace musan\attachments\migrations;

use yii\db\Migration;

/**
 * Class m180520_095904_attachment_uid
 */
class m200505_095904_attachment_watermark extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%attachment}}', 'is_watermarked', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%attachment}}', 'is_watermarked');
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
