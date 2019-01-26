<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$fields['id'] 	= ['type' => 'INT', 'constraint' => '32', 'auto_increment' => TRUE];		// ID PRIMARY
$fields['user_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];							
$fields['attr_key'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE];
$fields['attr_val'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE];

