<?php
	session_start();
	if(!empty($_SESSION['compte_id']))
	{
		require_once('includes/bdd.php');
		$requser = $bdd->prepare ("SELECT * FROM `users` WHERE `id`=:id");
		$requser->execute(array(
		':id' => $_SESSION['compte_id']));
		$user = $requser->fetch();
	}
	else
		$user['id'] = false;
?>