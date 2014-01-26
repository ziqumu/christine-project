<?php
	//connexion bdd
		require_once('includes/bdd.php');
	//info membre
		require_once('includes/users.php');
	//Si connecté alors redirection vers l'accueil
	if($user['id'] > 0)
	{
		$_SESSION['head_msg'] = 'Vous êtes déjà connecté';
		header("Location: index.php");
		exit;
	}
	//connection envoi
	$erreurs = '';
	if(isset($_POST['login']))
	{
		//verification des champs
		if(empty($_POST['login']) || empty($_POST['mdp']))
		{ 
			$erreurs = 'vous devez remplir tous les champs';
		}
		else
		{
			$reqLog = $bdd->prepare("SELECT mdp, id FROM `users` WHERE `login`= :login ");
			$reqLog->execute(array(
				':login' => $_POST['login']
				)) or die('Erreur requête verif login : L.'.__LINE__ );
			$infoMembre = $reqLog->fetch();
			
			$hash = crypt($_POST['mdp'],$infoMembre['mdp']);
			if($hash != $infoMembre['mdp'])
			{
				$erreurs = 'Mot de passe incorrect';
			}
			else
				$_SESSION['compte_id'] = $infoMembre['id'];
			
		}
	}
	//Connexion
	$titre='connexion';
	require('includes/header.php');
	
?>
			<h1>Connexion</h1>
		<form method="post" action="connexion.php">
			
				<span style="color:red;"><?php echo $erreurs;?></span>
				<label>Login : <input type="text" name="login" required ></label><br/>
				<label>Mot de passe : <input name="mdp" type="password" required></label><br/>
							
				<input type="submit" value="Valider">
			
		</form>
<?php
	require('includes/footer.php');
?>
		