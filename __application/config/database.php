<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = '';
$query_builder = TRUE;

$db['mariadb'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => 'root',
	'password' => 'Admin123',
	'database' => 'dynamic_menu',
	'dbdriver' => 'mysqli',
	'port' 	   => '3306',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => IS_LOCAL,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['postgres'] = array(
	'dsn'	=> 'pgsql:host=localhost;port=5432;dbname=dynamic_menu;user=postgres;password=Admin123',
	'hostname' => '',
	'username' => '',
	'password' => '',
	'database' => '',
	'dbdriver' => 'pdo',
	'port' 	   => '',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => IS_LOCAL,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
