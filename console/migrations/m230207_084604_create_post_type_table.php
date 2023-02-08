<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post_type}}`.
 */
class m230207_084604_create_post_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%post_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%post_type}}');
    }
}
