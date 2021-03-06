#!/usr/local/php7.1/bin/php
<?php
/**
 * swoole server
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

define('ROOT_PATH', __DIR__);
define('APP_DIR', ROOT_PATH . '/app');
//define('APPLICATION_ENV', $_ENV['MSF_ENV'] ?? 'dev');
define('APPLICATION_ENV', 'dev');
define('SYSTEM_NAME', 'grab');
define("WWW_DIR", realpath(dirname(__FILE__) . '/..'));
define('RUNTIME_DIR', WWW_DIR . '/runtime/' . SYSTEM_NAME);
!is_dir(RUNTIME_DIR) && @mkdir(RUNTIME_DIR, 0755, true);

require_once __DIR__ . '/vendor/autoload.php';

$cli = new \PG\MSF\MSFCli();
$cli->onConsoleRequest();