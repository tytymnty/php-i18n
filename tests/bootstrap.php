<?php
/**
 * @author tytymnty@gmail.com
 * @since 2015-12-05 17:29:44
 */

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

require  PROJECT_ROOT . '/vendor/autoload.php';