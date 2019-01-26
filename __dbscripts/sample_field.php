<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$fields['id'] 	= ['type' => 'INT', 'constraint' => '32', 'auto_increment' => TRUE];		// ID PRIMARY
$fields['is_active'] 	= ['type' => 'CHAR', 'constraint' => '1', 'default' => '1'];
$fields['created_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['created_by'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['updated_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['updated_by'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];
$fields['code'] = ['type' => 'VARCHAR', 'constraint' => '40', 'null' => TRUE];					// VARCHAR
$fields['name'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => FALSE];
$fields['description'] = ['type' => 'TEXT', 'null' => TRUE];														// TEXT
$fields['_id'] 	= ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];							// ID FOREIGN
$fields['_date'] = 	['type' => 'DATE', 'null' => TRUE];																	// DATE
$fields['percent'] 	= ['type' => 'NUMERIC', 'constraint' => '18,4', 'null' => TRUE];		// NUMERIC PERCENTAGE
$fields['amount'] = ['type' => 'NUMERIC', 'constraint' => '18,2', 'null' => TRUE];			// NUMERIC AMOUNT
$fields['heartbeat'] = ['type' => 'INT', 'constraint' => '32', 'null' => TRUE];					// DATE type INT
		
