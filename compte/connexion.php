<?php
	//connexion bdd
		require_once('../includes/bdd.php');
	//info membre
		require_once('../includes/users.php');
	//Si connecté alors redirection vers l'accueil
	if($user['id'] > 0)
	{
		$_SESSION['head_msg'] = 'Vous êtes déjà connecté';
		$_SESSION['head_class'] = 'erreur';
		header("Location: ../");
		exit;
	}
	//connection envoi
	$erreurs = '';
	if(isset($_POST['login']))
	{
		//verification des champs
		if(empty($_POST['login']) || empty($_POST['mdp']))
		{ 
			$_SESSION['head_msg'] = '';
			$_SESSION['head_class'] = 'erreur';
			header("Location: connexion.php");
			exit;
		}
		else
		{
			$reqLog = $bdd->prepare("SELECT login, mdp, id FROM `users` WHERE LOWER(`login`)= LOWER(:login) ");
			$reqLog->execute(array(
				':login' => $_POST['login']
				)) or die('Erreur requête verif login : L.'.__LINE__ );
			$infoMembre = $reqLog->fetch();
			
			$hash = crypt($_POST['mdp'],$infoMembre['mdp']);
			if($hash != $infoMembre['mdp'])
			{
				$_SESSION['head_msg'] = 'Mot de passe incorrect';
				$_SESSION['head_class'] = 'erreur';
				header("Location: connexion.php");
				exit;
			}
			else
			{
				$_SESSION['compte_id'] = $infoMembre['id'];
				$_SESSION['head_msg'] = 'Bonjour '.$infoMembre['login']. ' !';
				header("Location: ../");
				exit;
			}			
		}
	}
	//Connexion
	$titre='connexion';
	$dossier = 'compte';
	require('../includes/header.php');
	
?>
			<h1>Connexion</h1>
		<form method="post" action="connexion.php">
			
				<span style="color:red;"><?php echo $erreurs;?></span>
				<label>Login : <input type="text" name="login" required class="case"></label><br/>
				<label>Mot de passe : <input name="mdp" type="password" required class="case"></label><br/>
							
				<input type="submit" value="Valider" class="case">
			
		</form>
<?php
	require('../includes/footer.php');
?>
		