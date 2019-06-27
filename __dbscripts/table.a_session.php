<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$fields['id'] 	= ['type' => 'VARCHAR', 'constraint' => '255', 'null' => FALSE, 'unique' => TRUE];		// ID PRIMARY
$fields['created_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['expired_at'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE];
$fields['client_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];							// ID FOREIGN
$fields['user_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];							// ID FOREIGN
		
