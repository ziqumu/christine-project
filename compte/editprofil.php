<?php
	
	if(isset($_GET['id']) && is_numeric($_GET['id'])) //Demande de modification
	{
		require_once('../includes/bdd.php');
		require_once('../includes/users.php');
		//recup info topic
		$reqmembre = $bdd->prepare("SELECT `id`, `login`, `mdp`, `email`, `droit`, `sexe`,
		DATE_FORMAT(`datenais`,'%d-%m-%Y') as `datenais`, `region`, `ip`, `avatar` FROM `users` WHERE `id` = :id ");
		$reqmembre->execute(array(
			':id' => $_GET['id']
			)) or die('Erreur requête info membre : L.'.__LINE__ );

		//Verif de l'existence du membre
		if(!$membre = $reqmembre->fetch())
		{
			$_SESSION['head_msg'] = 'Le profil que vous avez essayé de modifier n\'existe pas ou plus';
			$_SESSION['head_class'] = 'erreur';
			header("Location: ../");
			exit;
		}

		//Verif des droits
		require_once('../includes/users.php');
		if($user['id'] == false || ($membre['id'] != $user['id'] && $user['droit'] != 1)) //Si il n'est pas connecté ou (ce n'est pas l'auteur du profil et qu'il n'est pas admin)
		{
			$_SESSION['head_msg'] = 'Vous n\'avez pas le droit de modifier ce profil';
			$_SESSION['head_class'] = 'erreur';
			header("Location: ../");
			exit;
		}

		if(isset($_POST['submit'])) //Execution modification profil
		{
			//Mot de passe
				if(!empty($_POST['oldmdp']) && !empty($_POST['mdp']) && !empty($_POST['conf']))
				{
					//Verif ancien mdp
					$hash = crypt($_POST['oldmdp'],$membre['mdp']);
					if($hash != $membre['mdp'])
					{
						$_SESSION['head_msg'] = 'L\'ancien mot de passe n\'est pas bon.';
						$_SESSION['head_class'] = 'erreur';
						header('Location: editprofil.php?id='.$membre['id']);
						exit;
					}
					//Verif format nouveau mdp
					if(strlen($_POST['mdp'])<5)
					{  
						$_SESSION['head_msg'] = 'Votre nouveau mot de passe doit faire 5 caractères ou plus.';
						$_SESSION['head_class'] = 'erreur';
						header('Location: editprofil.php?id='.$membre['id']);
						exit;
					}
					//Verif confirmation
					if($_POST['mdp'] != $_POST['conf'])
					{  
						$_SESSION['head_msg'] = 'Le nouveau mot de passe ne correspond pas à la confirmation';
						$_SESSION['head_class'] = 'erreur';
						header('Location: editprofil.php?id='.$membre['id']);
						exit;
					}
					//Generation du nouveau hash
						//Generation du "sel"
							$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
						//Hash blowfish (10 tours)
							$hash = crypt($_POST['mdp'],'$2y$10$'.$salt); 
				}
				else
				{
					$hash = $membre['mdp'];
				}
			//Verif email	
				if(empty($_POST['email']))
				{ 
					$_SESSION['head_msg'] = 'vous devez remplir tous les champs';
					$_SESSION['head_class'] = 'erreur';
					header('Location: editprofil.php?id='.$membre['id']);
					exit;
				}
				if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
				{  
					$_SESSION['head_msg'] = 'votre adresse email n\'est pas valide';
					$_SESSION['head_class'] = 'erreur';
					header('Location: editprofil.php?id='.$membre['id']);
					exit;
				}
				//verifier email  <255
				if(strlen($_POST['email'])>=255)
				{  
					$_SESSION['head_msg'] = 'Votre adresse email est trop longue';
					$_SESSION['head_class'] = 'erreur';
					header('Location: editprofil.php?id='.$membre['id']);
					exit;
				}
			//verifier sexe = 1 ou 0 sinon -> -1
				if(isset($_POST['sexe']) && ($_POST['sexe'] == 1 || $_POST['sexe'] == 0))
					$sexe = $_POST['sexe'];
				else
					$sexe = -1;
			//verifier date
				$dateTime = new DateTime('0000-00-00');
				if(!empty($_POST['datenais']))
				{  
					if(preg_match ('#^([0-9]{1,2})[^0-9]([0-9]{1,2})[^0-9]([0-9]{4})#', $_POST['datenais'], $matchesDate))
					{
						if(!$dateTime->setDate($matchesDate[3],$matchesDate[2],$matchesDate[1]))
						{
							$_SESSION['head_msg'] = 'Format de date de naissance non valide';
							$_SESSION['head_class'] = 'erreur';
							header('Location: editprofil.php?id='.$membre['id']);
							exit;
						}
					}
					elseif(preg_match ('#^([0-9]{4})[^0-9]([0-9]{2})[^0-9]([0-9]{2})#', $_POST['datenais'], $matchesDate))
					{
						if(!$dateTime->setDate($matchesDate[1],$matchesDate[2],$matchesDate[3]))
						{
							$_SESSION['head_msg'] = 'Format de date de naissance non valide';
							$_SESSION['head_class'] = 'erreur';
							header('Location: editprofil.php?id='.$membre['id']);
							exit;
						}
					}
					else
					{
						$_SESSION['head_msg'] = 'Format de date de naissance non valide';
						$_SESSION['head_class'] = 'erreur';
						header('Location: editprofil.php?id='.$membre['id']);
						exit;
					}
				}
			//region <50
				if(strlen($_POST['region'])>=50)
				{  
					$_SESSION['head_msg'] = 'Votre region doit faire moins de 50 caractères';
					$_SESSION['head_class'] = 'erreur';
					header('Location: editprofil.php?id='.$membre['id']);
					exit;
				}
			//Verif avatar
				if($_FILES["avatar"]["error"] != 4) //si un fichier a été envoyé
				{

					$taille = filesize($_FILES['avatar']['tmp_name']);
					$extensions = array('.png', '.gif', '.jpg', '.jpeg');
					$extension = strrchr($_FILES['avatar']['name'], '.'); 
					list($width, $height, $type, $attr) = getimagesize($_FILES['avatar']['tmp_name']);
					if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
					{
						$_SESSION['head_msg'] = 'Le fichier doit être de type png, gif, jpg ou jpeg';
						$_SESSION['head_class'] = 'erreur';
						header('Location: editprofil.php?id='.$membre['id']);
						exit;
					}
					elseif($taille > 100000)
					{
						$_SESSION['head_msg'] = 'Le fichier doit faire moins de 100ko';
						$_SESSION['head_class'] = 'erreur';
						header('Location: editprofil.php?id='.$membre['id']);
						exit;
					}
					elseif($width>100 || $height>100)
					{
						$_SESSION['head_msg'] = 'L\'avatar doit avoir une largeur et une longeur inférieur à 100px';
						$_SESSION['head_class'] = 'erreur';
						header('Location: editprofil.php?id='.$membre['id']);
						exit;
					}
				}
				else
				{
					$extension = $membre['avatar'];
				}
			//maj du profil

			$reqLog = $bdd->prepare("UPDATE `users` SET  `mdp`=:hash,`sexe`=:sexe,`datenais`=:datenais,`email`=:email,`region`=:region,`avatar`=:avatar WHERE `id` = :id");
			$reqLog ->execute(array(
				':hash' => $hash,
				':sexe' => $sexe,
				':datenais' => $dateTime->format('Y-m-d'),
				':region' => $_POST['region'],
				':email' => $_POST['email'] ,
				':avatar' => $extension,
				':id' => $membre['id']
			)) or die('Erreur maj profil : L.'.__LINE__ );
			//Upload avatar
			if($_FILES["avatar"]["error"] != 4)//Si avatar
			{
					if(file_exists('../uploads/' . $membre['id']. $membre['avatar']))
						unlink('../uploads/' . $membre['id']. $membre['avatar']);
					move_uploaded_file($_FILES['avatar']['tmp_name'], '../uploads/' . $membre['id']. $extension);
			}
			//retour utilisateur
				$_SESSION['head_msg'] = 'Votre profil a bien été modifié.';
				header('Location: editprofil.php?id='.$membre['id']);
				exit;

		}
		else //Mode modification
		{
				$titre= 'Modification du profil';
				$dossier='compte';
				require('../includes/header.php');
				
				?>
					<h1>Modification du profil</h1>
				<form method="post" action="editprofil.php?id=<?php echo $membre['id'];?>"  enctype="multipart/form-data">
				<div class="contenu">
					<h3> Vos informations de connexion</h3> 
					<strong>Ne remplissez les mots de passes que si vous voullez en changer ! </strong><br/>
					<label>Votre ancien mot de passe : <br/><input name="oldmdp" type="password" autocomplete="off"></label><br/>
					<label>Nouveau mot de passe : <br/><input name="mdp" type="password" ></label><br/>
					<label>Confirmation du nouveau: <br/><input name="conf" type="password" autocomplete="off"></label><br/>
					<label>Adresse mail : <br/><input type="email" name="email" size="50px" autocomplete="off"  value="<?php echo $membre['email'];?>"></label><br/>

					<h3> Vos informations personnelles</h3>
					<?php 
						$sexe[0]=$sexe[1]='';
						$sexe[$membre['sexe']]='checked';
					?>
					Sexe : <br/><label><input type="radio" name="sexe" value="0" <?php echo $sexe[0];?>> Homme</label> 
					<label><input type="radio" name="sexe" value="1" <?php echo $sexe[1];?>> Femme</label><br/>
					<label >Date de naissance : <br/><input type="date" name="datenais" placeholder="jj-mm-aaaa" value="<?php echo $membre['datenais'];?>" ></label><br/>
					<label >Région: <br/><input name="region" value="<?php echo $membre['region'];?>"></label><br/>
					<label for="avatar">Avatar (jpg, png ou gif | max. 100 Ko | 100x100px max) :</label><br />
					     <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
					<input type="file" name="avatar" id="avatar" /><br />
					Avatar actuel : <br/>
					<img  src="../uploads/<?php echo  $membre['id'].$membre['avatar'];?>" alt="avatar"/><br/> 
					<input type="submit" name="submit" value="Valider" >
				</div>
				</form>
				<?php
				require('../includes/footer.php');
		}


	}
	//404
	else
	{
		require_once('../includes/users.php');
		$_SESSION['head_msg'] = 'Page non trouvé';
		$_SESSION['head_class'] = 'erreur';
		header("Location: ../index.php");
		exit;
	}
