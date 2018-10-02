<?
define("ENV", "test"); // on tho host provider chage to 'define("ENV", "prod");'
require_once "config_" . ENV . ".php";
define("LOG_SET_FILE", "configs/log4php_" . ENV . ".xml");
Logger::configure(LOG_SET_FILE);

