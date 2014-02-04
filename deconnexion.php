<?php
	//info membre
		require_once('includes/users.php');
	// deconnexion
		$_SESSION['compte_id']=0;
	//redirection
		$_SESSION['head_msg'] = 'Vous êtes bien déconnecté. A bientôt !';
		header("Location: ./");
?>