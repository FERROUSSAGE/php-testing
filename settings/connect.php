<?php
    session_start();
    
    $host = 'localhost';
    $username = 'id15725366_root';
    $password = '~8tF+<P)9xnEN0Dd';
    $db_name = 'id15725366_anilend';
    $charset = 'utf8';

    $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::
		ERRMODE_EXCEPTION,
	    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::
		FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $pdo = new PDO($dsn, $username, $password, $options);


