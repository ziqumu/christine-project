<?php
	//connexion bdd
		require_once('../includes/bdd.php');
	//info membre
		require_once('../includes/users.php');
	//Si connecté alors redirection vers l'accueil
	if($user['id'] > 0)
	{
		$_SESSION['head_msg'] = 'Vous êtes déjà inscrit';
		$_SESSION['head_class'] = 'erreur';
		header("Location: ../");
		exit;
	}
	//Inscription envoi
	if(isset($_POST['login']))
	{
		//verification des champs
		if(empty($_POST['login']) || empty($_POST['mdp']) || empty($_POST['conf']) || empty($_POST['email']) || !isset($_POST['datenais']) || !isset($_POST['region']) )
		{ 
			
			$_SESSION['head_class'] = 'erreur';
			$_SESSION['head_msg'] = 'Vous devez remplir tous les champs';
			header("Location: inscription.php");
			exit;

		}
		elseif(!isset($_POST['charte']))
		{
			
			$_SESSION['head_class'] = 'erreur';
			$_SESSION['head_msg'] = 'Vous devez signez la charte';
			header("Location: inscription.php");
			exit;

		}
		elseif($_POST['mdp'] != $_POST['conf'])		
		{
			
			$_SESSION['head_class'] = 'erreur';
			$_SESSION['head_msg'] = 'La confirmation ne correspond pas à votre mot de passe';
			header("Location: inscription.php");
			exit;

		}
		elseif(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{  
			
			$_SESSION['head_class'] = 'erreur';
			$_SESSION['head_msg'] = 'votre adresse email n\'est pas valide';
			header("Location: inscription.php");
			exit;

		}
		//verifier taille mdp >5
		elseif(strlen($_POST['mdp'])<5)
		{  
			
			$_SESSION['head_class'] = 'erreur';
			$_SESSION['head_msg'] = 'Votre mot de passe doit faire 5 caractères ou plus';
			header("Location: inscription.php");
			exit;

		}
		//verifier taille pseudo <25 et >2
		elseif(strlen($_POST['login'])>=25 && strlen($_POST['login'])<=2)
		{  
			
			$_SESSION['head_class'] = 'erreur';
			$_SESSION['head_msg'] = 'Votre login doit faire entre 2 et 25 caractères';
			header("Location: inscription.php");
			exit;

		}
		//verifier email  <255
		elseif(strlen($_POST['email'])>=255)
		{  
			
			$_SESSION['head_class'] = 'erreur';
			$_SESSION['head_msg'] = 'Votre adresse email est incorrect';
			header("Location: inscription.php");
			exit;

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
							
					$_SESSION['head_class'] = 'erreur';
					$_SESSION['head_msg'] = 'Format de date de naissance non valide';
					header("Location: inscription.php");
					exit;

				}
			}
			elseif(preg_match ('#^([0-9]{4})[^0-9]([0-9]{2})[^0-9]([0-9]{2})#', $_POST['datenais'], $matchesDate))
			{
				if(!$dateTime->setDate($matchesDate[1],$matchesDate[2],$matchesDate[3]))
				{
					
					$_SESSION['head_class'] = 'erreur';
					$_SESSION['head_msg'] = 'Format de date de naissance non valide';
					header("Location: inscription.php");
					exit;

				}
			}
			else
			{
				$_SESSION['head_class'] = 'erreur';
				$_SESSION['head_msg'] = 'Format de date de naissance non valide';
				header("Location: inscription.php");
				exit;
			}

		}
		//region <50
		if(empty($erreurs) && strlen($_POST['region'])>=50)
		{  
			
			$_SESSION['head_class'] = 'erreur';
			$_SESSION['head_msg'] = 'votre region doit faire moins de 50 caractères';
			header("Location: inscription.php");
			exit;

		}
		//verifier si le pseudo et email n'existe pas
		$extension = '';
		if(empty($erreurs))
		{
			$reqLog = $bdd->prepare("SELECT (SELECT COUNT(*) FROM `users` WHERE `login`= :login ) as nbLogin, (SELECT COUNT(*) FROM `users` WHERE `email`= :email ) as nbEmail");
			$reqLog->execute(array(
				':login' => $_POST['login'],
				':email' => $_POST['email']
				)) or die('Erreur requête verif login : L.'.__LINE__ );
			$counts = $reqLog->fetch();
			if($counts['nbLogin'] > 0)
			{
				$_SESSION['head_class'] = 'erreur';
				$_SESSION['head_msg'] = 'Votre login est déjà utilisé';
				header("Location: inscription.php");
				exit;
			}
			elseif($counts['nbEmail'] > 0)
			{
				$_SESSION['head_class'] = 'erreur';
				$_SESSION['head_msg'] = 'Un compte avec cette adresse email existe déjà';
				header("Location: inscription.php");
				exit;
			}

		}
		//verifications du fichier d'avtar
		if(empty($erreurs) && $_FILES["avatar"]["error"] != 4) //si un fichier a été envoyé
		{

			$taille = filesize($_FILES['avatar']['tmp_name']);
			$extensions = array('.png', '.gif', '.jpg', '.jpeg');
			$extension = strrchr($_FILES['avatar']['name'], '.'); 
			list($width, $height, $type, $attr) = getimagesize($_FILES['avatar']['tmp_name']);
			if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
			{
			     
				$_SESSION['head_class'] = 'erreur';
				$_SESSION['head_msg'] = 'Le fichier doit être de type png, gif, jpg ou jpeg';
				header("Location: inscription.php");
				exit;

			}
			elseif($taille > 100000)
			{
			     
				$_SESSION['head_class'] = 'erreur';
				$_SESSION['head_msg'] = 'Le fichier doit faire moins de 100ko';
				header("Location: inscription.php");
				exit;

			}
			elseif($width>100 || $height>100)
			{
			     
				$_SESSION['head_class'] = 'erreur';
				$_SESSION['head_msg'] = 'L\'avatar doit avoir une largeur et une longeur inférieur à 100px';
				header("Location: inscription.php");
				exit;

			}
		}
		if(empty($erreurs))
		{
			//Generation du "sel"
				$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
			//Hash blowfish (10 tours)
				$mdp = crypt($_POST['mdp'],'$2y$10$'.$salt); 
			//Inscription
				$reqLog = $bdd->prepare("INSERT INTO users VALUES (NULL,:login,:mdp,:email,0,:sexe,:datenais,:region,NOW(),NOW(),:ip,:extavatar)");
				$reqLog->execute(array(
					':login' => $_POST['login'],
					':mdp' => $mdp,
					':email' => $_POST['email'],
					':sexe' => $sexe,
					':datenais' => $dateTime->format('Y-m-d'),
					':region' => $_POST['region'],
					':ip' => $_SERVER['REMOTE_ADDR'],
					':extavatar' => $extension
				)) or die('Erreur requête inscription : L.'.__LINE__ );
			if($_FILES["avatar"]["error"] != 4)//Si avatar
			{
				//Recup de l'id
					$id = $bdd->lastInsertId();
				//upload avatar
					move_uploaded_file($_FILES['avatar']['tmp_name'], '../uploads/' . $id. $extension);
			}
			//Retour utilisateur
				$_SESSION['head_msg'] = 'Merci pour votre inscription, vous pouvez vous connecter.';
				header("Location: ../");
				exit;
		}
	}
	//Inscription formulaire
	$titre='Inscription';
	$dossier='compte';
	require('../includes/header.php');
	
?>
			<h1>Inscription</h1>
			
				<form method="post" action="inscription.php"  enctype="multipart/form-data">
				
					<p>Ce formulaire d'inscription vous permet de créer un compte pour consulter et contribuer au Forum</p>
					<p>Remplissez les champs ci-dessous</p>
				
				<div class="contenu">
					<h3> Vos informations de connexion (obligatoires)</h3>
					<label>Login : <br/><input type="text" name="login" required  autocomplete="off" ></label><br/>
					<label>Mot de passe : <br/><input name="mdp" type="password" required ></label><br/>
					<label>Confirmation : <br/><input name="conf" type="password" required ></label><br/>
					<label>Adresse mail : <br/><input type="email" name="email" size="50px" required ></label><br/>

					<h3> Vos informations personnelles (faculatives)</h3>
					Sexe : <br/><label><input type="radio" name="sexe" value="0" > Homme</label> 
					<label><input type="radio" name="sexe" value="1"> Femme</label><br/>
					<label >Date de naissance : <br/><input type="date" name="datenais" placeholder="jj-mm-aaaa" ></label><br/>
					<label >Région: <br/><input name="region"></label><br/>
					<label for="avatar">Avatar (jpg, png ou gif | max. 100 Ko | 100x100px max) :</label><br />
					     <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
					<input type="file" name="avatar" id="avatar" /><br />
					
					<h3> Validation </h3>
					Acceptez-vous la charte? <a href="charte.php" target="_blank" >Lire la charte</a><br/>
					<label><input type="checkbox" name="charte" required>oui,j'accepte</label><br/> 
					<input type="submit" value="Valider" >
				</div>
				</form>
			</div>
<?php
	require('../includes/footer.php');
?>
		