<?php

$db_driver = "mysql";
$host = "localhost";
$database = "dbadminpanel";
$dsn = "$db_driver:host=$host; dbname=$database";

$username = "root";
$password = "";

try {
	$admindbh = new PDO ($dsn, $username, $password);
	// echo "Connect to database:". $database;
}
catch (PDOException $e) {
	echo "Error". $e->getMessage();
	die(); 
}