<?php
	session_start();
	if(!empty($_SESSION['compte_id']))
	{
		require_once(__DIR__.'/bdd.php');
		$requser = $bdd->prepare ("SELECT * FROM `users` WHERE `id`=:id");
		$requser->execute(array(
		':id' => $_SESSION['compte_id']));
		$user = $requser->fetch();
		///Maj du compte
		$requser = $bdd->prepare ("UPDATE `users` SET `lastconnexion`=NOW(),`ip`=:ip WHERE `id`=:id");
		$requser->execute(array(
			':id' => $_SESSION['compte_id'],
			':ip' => $_SERVER["REMOTE_ADDR"]));
		//Verif si banni
		if($user['droit'] == -1)
		{
			// deconnexion
				$_SESSION['compte_id']=0;
				$_SESSION['head_msg'] = 'Vous êtes banni !';
				$_SESSION['head_class'] = 'erreur';
				$user['id'] = false;
		}

	}
	else
		$user['id'] = false;
?>