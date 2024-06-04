<?php
require("vendor/autoload.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

$openapi = \OpenApi\Generator::scan(['backend/rest']);

header('Content-Type: application/json');
echo $openapi->toJson();
