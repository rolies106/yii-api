<?php

class m130126_123637_create_user_options extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('{{user_options}}', array(
            'user_id' => 'int(11)',
            'option_name' => 'varchar(255)',
            'option_value' => 'varchar(255)',
            'PRIMARY KEY (`user_id`, `option_name`)'
        ));

        // $this->createIndex('identity', '{{user_options}}', 'user_id, option_name');
	}

	public function safeDown()
	{
		$this->dropTable('{{user_options}}');
	}
}