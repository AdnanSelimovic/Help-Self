<?php
require 'vendor/autoload.php'; //run autoloader

Flight::route('/', function(){
    include 'pages/landing.php';
});

Flight::route('/registration', function(){
    include 'pages/registration.php';
});

Flight::route('/login', function(){
    include 'pages/login.php';
});

Flight::route('/home', function(){
    include 'pages/home.php';
});

Flight::route('/habits', function(){
    include 'pages/habits.php';
});

Flight::route('/habits/edit', function(){
    include 'pages/edit.php';
});

Flight::route('/forum', function(){
    include 'pages/forum.php';
});

Flight::route('/profile', function(){
    include 'pages/profile.php';
});

Flight::route('/info', function(){
    include 'pages/info.php';
});

Flight::route('/settings', function(){
    include 'pages/settings.php';
});

Flight::start();
