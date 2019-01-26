<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$fields['id'] 	= ['type' => 'INT', 'constraint' => '32', 'auto_increment' => TRUE];		// ID PRIMARY
$fields['user_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];							
$fields['dashboard_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];							
$fields['line_no'] = ['type' => 'INT', 'constraint' => '16', 'default' => 0];
$fields['created_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['created_by'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['updated_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['updated_by'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
