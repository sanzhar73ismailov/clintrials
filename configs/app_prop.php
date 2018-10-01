<?
define("ENV", "dev"); // on tho host provider chage to 'define("ENV", "prod");'
require_once "config_" . ENV . ".php";
define("LOG_SET_FILE", "log4php_" . ENV . ".xml");

