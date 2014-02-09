<?php
	//connexion bdd
		require_once('includes/bdd.php');
	//info membre
		require_once('includes/users.php');
	//Si connecté alors redirection vers l'accueil
	if($user['id'] > 0)
	{
		$_SESSION['head_msg'] = 'Vous êtes déjà inscrit';
		$_SESSION['head_class'] = 'erreur';
		header("Location: index.php");
		exit;
	}
	//Inscription envoi
	$erreurs = '';
	if(isset($_POST['login']))
	{
		//verification des champs
		if(empty($_POST['login']) || empty($_POST['mdp']) || empty($_POST['conf']) || empty($_POST['email']) || !isset($_POST['datenais']) || !isset($_POST['region']) )
		{ 
			$erreurs = 'vous devez remplir tous les champs';
		}
		elseif(!isset($_POST['charte']))
		{
			$erreurs = 'vous devez signez la charte';
		}
		elseif($_POST['mdp'] != $_POST['conf'])		
		{
			$erreurs = 'confirmation ne correspond pas à votre mot de passe';
		}
		elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{  
			$erreurs = 'votre adresse email n\'est pas valide';
		}
		//verifier taille mdp >5
		elseif(strlen($_POST['mdp'])<5)
		{  
			$erreurs = 'votre mot de passe doit faire plus de 4 caractères';
		}
		//verifier taille pseudo <25 et >2
		elseif(strlen($_POST['login'])>=25 && strlen($_POST['login'])<=2)
		{  
			$erreurs = 'votre login doit faire entre 2 et 25 caractères';
		}
		//verifier email  <255
		elseif(strlen($_POST['email'])>=255)
		{  
			$erreurs = 'votre adresse email est incorrect';
		}
		//verifier sexe = 1 ou 0 sinon -> -1
		else
		{
			if(isset($_POST['sexe']) && ($_POST['sexe'] == 1 || $_POST['sexe'] == 0))
				$sexe = $_POST['sexe'];
			else
				$sexe = -1;
		}
		//verifier date
		$dateTime = new DateTime('0000-00-00');
		if(empty($erreurs) && !empty($_POST['datenais']))
		{  
			if(preg_match ('#^([0-9]{1,2})[^0-9]([0-9]{1,2})[^0-9]([0-9]{4})#', $_POST['datenais'], $matchesDate))
			{
				if(!$dateTime->setDate($matchesDate[3],$matchesDate[2],$matchesDate[1]))
				{
					$erreurs = 'Format de date de naissance non valide';
				}
			}
			elseif(preg_match ('#^([0-9]{4})[^0-9]([0-9]{2})[^0-9]([0-9]{2})#', $_POST['datenais'], $matchesDate))
			{
				if(!$dateTime->setDate($matchesDate[1],$matchesDate[2],$matchesDate[3]))
				{
					$erreurs = 'Format de date de naissance non valide';
				}
			}
			else
				$erreurs = 'Format de date de naissance non valide';
		}
		//region <50
		if(empty($erreurs) && strlen($_POST['region'])>=50)
		{  
			$erreurs = 'votre region doit faire moins de 50 caractères';
		}
		//verifier si le pseudo et email n'existe pas
		if(empty($erreurs))
		{
			$reqLog = $bdd->prepare("SELECT (SELECT COUNT(*) FROM `users` WHERE `login`= :login ) as nbLogin, (SELECT COUNT(*) FROM `users` WHERE `email`= :email ) as nbEmail");
			$reqLog->execute(array(
				':login' => $_POST['login'],
				':email' => $_POST['email']
				)) or die('Erreur requête verif login : L.'.__LINE__ );
			$counts = $reqLog->fetch();
			if($counts['nbLogin'] > 0)
				$erreurs = 'Votre login est déjà utilisé';
			elseif($counts['nbEmail'] > 0)
				$erreurs = 'Un compte avec cette adresse email existe déjà';
		}
		if(empty($erreurs))
		{
			//Generation du "sel"
				$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
			//Hash blowfish (10 tours)
				$mdp = crypt($_POST['mdp'],'$2y$10$'.$salt); 
			//Inscription
				$reqLog = $bdd->prepare("INSERT INTO users VALUES (NULL,:login,:mdp,:email,0,:sexe,:datenais,:region,NOW(),NOW(),:ip)");
				$reqLog ->execute(array(
					':login' => $_POST['login'],
					':mdp' => $mdp,
					':email' => $_POST['email'],
					':sexe' => $sexe,
					':datenais' => $dateTime->format('Y-m-d'),
					':region' => $_POST['region'],
					':ip' => $_SERVER['REMOTE_ADDR']
				)) or die('Erreur requête inscription : L.'.__LINE__ );
			//Retour utilisateur
				$_SESSION['head_msg'] = 'Merci pour votre inscription, vous pouvez vous connecter.';
				header("Location: index.php");
				exit;
		}
	}
	//Inscription formulaire
	$titre='Inscription';
	require('includes/header.php');
	
?>
		
			<h1>Inscription</h1>
			
				<form method="post" action="inscription.php">
				
					<p>Ce formulaire d'inscription vous permet de créer un compte pour consulter et contribuer au Forum</p>
					<p>Remplissez les champs ci-dessous</p>
				
				<div class="contenu">
					<span><?php echo $erreurs;?></span>
					<h3> Vos informations de connexion (obligatoires)</h3>
					Login :<div> <input type="text" name="login" required  autocomplete="off" ></div>
					<label>Mot de passe :<div> <input name="mdp" type="password" required ></div></label>
					<label>Confirmation :<div> <input name="conf" type="password" required ></div></label>
					<label>Adresse mail :<div> <input type="email" name="email" required size="50px"></div></label>

					<h3> Vos informations personnelles (faculatives)</h3>
					<label>Sexe :</label>
					<div><input type="radio" name="sexe" value="0" > Homme
					<input type="radio" name="sexe" value="1"> Femme</div>
					
					<label >Date de naissance : </label><div><input type="date" name="datenais" placeholder="jj-mm-aaaa" ><div/>
					<label >Région: <div><input name="region"></div></label>
				
					
					Acceptez-vous la charte? <a href="charte.php" target="_blank" >Lire la charte</a><br/>
					<label><input type="checkbox" name="charte" >oui,j'accepte</label><br/> 
					<input type="submit" value="Valider" >
				</div>
				</form><br/>
			</div>
<?php
	require('includes/footer.php');
?>
		