<?php
require 'vendor/autoload.php'; //run autoloader

Flight::route('/', function(){
    include 'frontend/index.html';
});

Flight::start();
