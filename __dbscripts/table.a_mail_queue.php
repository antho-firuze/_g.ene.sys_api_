<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$fields['id'] 	= ['type' => 'INT', 'constraint' => '32', 'auto_increment' => TRUE];		// ID PRIMARY
$fields['created_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['lasttry_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['_from'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE];					// VARCHAR
$fields['_to'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE];					// VARCHAR
$fields['_cc'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE];					// VARCHAR
$fields['_bcc'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE];					// VARCHAR
$fields['_subject'] = ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE];					// VARCHAR
$fields['_body'] = ['type' => 'TEXT', 'null' => TRUE];														// TEXT
$fields['is_test'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '1'];
$fields['is_sent'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '0'];
$fields['trying'] = ['type' => 'INT', 'constraint' => '32', 'default' => 0];					
$fields['status'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE];					// VARCHAR
$fields['error_message'] = ['type' => 'TEXT', 'null' => TRUE];														// TEXT
