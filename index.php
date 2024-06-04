<?php
require 'vendor/autoload.php';
require 'backend/rest/Controller.php';

use HelpSelf\Controller;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$controller = new Controller();
$controller->initRoutes();

Flight::start();
