<?php
require 'vendor/autoload.php'; //run autoloader

Flight::route('/', function(){
    echo 'Hello world';
});

Flight::start();
?>