<?php 

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$host = 'localhost'; 
$db = 'activities'; 
$user = 'root'; 
$password = ''; 