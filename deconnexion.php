<?php
	//info membre
		require_once('includes/users.php');
	// deconnexion
		$_SESSION['compte_id']=0;
	//redirection
		$_SESSION['head_msg'] = 'A bientôt !';
		header("Location: index.php");
?>