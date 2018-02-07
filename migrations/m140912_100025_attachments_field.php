<?php

namespace musan\attachments\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m140912_100025_attachments_field extends Migration
{
    public function up()
    {
        $this->addColumn('attachment', 'attr_name', Schema::TYPE_STRING);
    }

    public function down()
    {
        $this->dropColumn('attachment', 'attr_name');
    }
}
