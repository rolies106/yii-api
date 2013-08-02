<?php

class m130802_073037_create_table extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('{{oauth2_clients}}', array(
            'client_id' => 'varchar(20) NOT NULL',
            'client_secret' => 'varchar(20) NOT NULL',
            'redirect_uri' => 'varchar(200) NOT NULL',
            'app_owner_user_id' => 'int(11) unsigned NOT NULL',
            'app_title' => 'varchar(255) NOT NULL',
            'app_desc' => 'text',
            'status' => 'int(1) unsigned NOT NULL DEFAULT "0"',
            'created_at' => 'timestamp NOT NULL default CURRENT_TIMESTAMP',
            'PRIMARY KEY (`client_id`)'
        ));

        $this->createTable('{{oauth2_tokens}}', array(
            'oauth_token' => 'varchar(40) NOT NULL',
            'token_type' => "enum('code', 'access', 'refresh') default 'code'",
            'client_id' => 'varchar(20) NOT NULL',
            'user_id' => 'int(11) unsigned NOT NULL',
            'expires' => 'int(11) NOT NULL',
            'redirect_uri' => "varchar(200) NOT NULL default 'oob'",
            'scope' => 'varchar(200) DEFAULT NULL',
            'created_at' => 'timestamp NOT NULL default CURRENT_TIMESTAMP',
            'PRIMARY KEY (`oauth_token`)'
        ));        

        $this->insert('{{oauth2_clients}}', array(
        		'client_id' => '1234567890',
        		'client_secret' => '1234567890',
        		'redirect_uri' => 'http://api-client.local/test/callback'
        		'app_owner_user_id' => 1,
        		'app_title' => 'Application Sample',
        		'status' => '1'
        	)
        );
	}

	public function safeDown()
	{
		$this->dropTable('{{oauth2_clients}}');
		$this->dropTable('{{oauth2_tokens}}');
	}
}