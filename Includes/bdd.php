<?php
	$mysql_host = 'localhost';
	$mysql_base = 'forum';
	$mysql_user = 'root';
	$mysql_password = '';
	
//Connexion
	try 
	{
		$bdd = new PDO('mysql:host='.$mysql_host.';dbname='.$mysql_base, $mysql_user, $mysql_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		$bdd->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
	} 
	catch (PDOException $e) 
	{
		
		echo 'Erreur de connexion à la base de donnée.';
	}
	
	
	//$bdd->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);//