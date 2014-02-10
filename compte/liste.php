<?php
	//Info membre
	require('../includes/bdd.php');
	require('../includes/users.php');
	if($user['droit'] == 1)
	{
		$reqmembre = $bdd->prepare("SELECT `id`, `login`, `mdp`, `email`, `droit`, `sexe`, `datenais`, `region`, `inscription`, `lastconnexion`, `ip`, `avatar` FROM `users` ORDER BY id ASC");
		$reqmembre->execute() or die('Erreur requête info membres : L.'.__LINE__ );


		$titre='Liste des membres';
		$dossier  = 'compte';
		require('../includes/header.php');
		echo '<h1>Liste des membres</h1>';
		while($membre = $reqmembre->fetch())
		{
			echo '<a href="profil.php?id='.$membre['id'].'">'.$membre['login'].'</a>';
			if($membre['droit'] == 1)
				echo ' (Administrateur)';
			elseif($membre['droit'] == -1)
				echo ' (Banni)';
			echo '<br/>';
		}
	}
	else
	{
		$_SESSION['head_msg'] = 'Vous n\'avez pas le droit de voir cette page';
		$_SESSION['head_class'] = 'erreur';
		header("Location: ../");
		exit;
	}
//footer
	require('../includes/footer.php');
	
	
?>