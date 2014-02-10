<?php
	//Info membre
	require('../includes/bdd.php');
	require('../includes/users.php');
	if($user['droit'] == 1)
	{
		if(isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['value']) && ($_GET['value'] == -1 || $_GET['value'] == 0 || $_GET['value'] == 1))
		{

			$reqLog = $bdd->prepare("UPDATE `users` SET  `droit`=:value WHERE `id` = :id");
			$reqLog ->execute(array(
				':value' => $_GET['value'],
				':id' => $_GET['id']
			)) or die('Erreur maj profil : L.'.__LINE__ );

			$_SESSION['head_msg'] = 'Les droits du membre ont bien été modifié.';
			header("Location: profil.php?id=".$_GET['id']);
			exit;
		}
		else
		{
			$_SESSION['head_msg'] = 'Votre demande n\'a pas été comprise.';
			$_SESSION['head_class'] = 'erreur';
			header("Location: ../");
			exit;
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