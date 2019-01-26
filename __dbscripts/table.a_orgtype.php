<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$fields['id'] 	= ['type' => 'INT', 'constraint' => '32', 'auto_increment' => TRUE];		// ID PRIMARY
$fields['is_active'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '1'];
$fields['created_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['created_by'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['updated_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['updated_by'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['code'] = ['type' => 'VARCHAR', 'constraint' => '40', 'null' => TRUE];
$fields['name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => FALSE, 'unique' => TRUE];
$fields['description'] = ['type' => 'TEXT', 'null' => TRUE];

