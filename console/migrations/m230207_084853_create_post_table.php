<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m230207_084853_create_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(300)->notNull(),
            'description' => 'LONGTEXT',
            'post_type_id' => $this->integer(),
            'created_by' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_by' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey('FK_post_user_created_by', '{{%post}}', 'created_by', '{{%user}}', 'id');
        $this->addForeignKey('FK_post_post_type_id', '{{%post}}', 'post_type_id', '{{%post_type}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK_post_user_created_by', '{{%post}}');
        $this->dropForeignKey('FK_post_post_type_id', '{{%post_type}}');
        $this->dropTable('{{%post}}');
    }
}
