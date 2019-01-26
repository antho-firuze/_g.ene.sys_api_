<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$fields['id'] = ['type' => 'INT', 'constraint' => '32', 'auto_increment' => TRUE];
$fields['created_at'] = ['type' => 'TIMESTAMP', 'null' => TRUE];
$fields['username'] = ['type' => 'VARCHAR', 'constraint' => '40', 'null' => TRUE];
$fields['password'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
$fields['ip_address'] = ['type' => 'VARCHAR', 'constraint' => '60', 'null' => TRUE];
