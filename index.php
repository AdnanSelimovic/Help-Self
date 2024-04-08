<?php
require 'vendor/autoload.php'; //run autoloader

Flight::route('/', function(){
    include 'index.html';
});

Flight::start();
