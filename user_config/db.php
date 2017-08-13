<?php
/**
 * DB設定定義関数
 *
 * DB設定を獲得する関数です。複数DBの同時接続を可能にするためです
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
*/
function acwork_db($target)
{
	$param = array();
	/**
	 * 規定値は''です
	*/
	$param[''] = array(
		'dsn' => 'pgsql:dbname=simple_framework;host=localhost',
		'username' => 'postgres',
		'password' => '123456',
		'driver_options' => array(PDO::ATTR_PERSISTENT => false)
		);
		
	$param['DTP_SERVER'] = array(
	  'dsn' => 'pgsql:dbname=postgres;host=192.168.1.8;port=5432',
	  'username' => 'postgres',
	  'password' => '123456',
	  'driver_options' => array(PDO::ATTR_PERSISTENT => false)
	  );
	
	return $param[$target];
}
/* 終わり */
