<?php
	
	if(isset($_GET['id']) && is_numeric($_GET['id'])) //Demande de modification
	{
		require('../includes/bdd.php');
		//recup info topic
		$reqmessage = $bdd->prepare("SELECT `id`, `contenu`, `datepost`, `id_users`, `id_topics` FROM `messages` WHERE `id` = :id ");
		$reqmessage->execute(array(
			':id' => $_GET['id']
			)) or die('Erreur requête info message : L.'.__LINE__ );

		//Verif de l'existence du message
		if(!$message = $reqmessage->fetch())
		{
			require_once('../includes/users.php');
			$_SESSION['head_msg'] = 'Le message que vous avez essayé de modifier n\'existe pas ou plus';
			header("Location: ../");
			exit;
		}

		//Verif des droits
		require_once('../includes/users.php');
		if($user['id'] == false || ($message['id_users'] != $user['id'] && $user['droit'] != 1)) //Si il n'est pas connecté ou (ce n'est pas l'auteur du topic et qu'il n'est pas admin)
		{
			$_SESSION['head_msg'] = 'Vous n\'avez pas le droit de modifier ce message';
			header("Location: topic.php?id=".$message['id_topics']);
			exit;
		}

		//Execution des actions
		if(isset($_POST['message'])) //Execution modification message
		{
			//Verif longueur message
			if(strlen($_POST['message']) < 3)
			{
				$_SESSION['head_msg'] = 'Votre message doit comporter au moins 3 caractères.';
				$_SESSION['head_class'] = 'erreur';
				header('Location: editmessage.php?id='.$message['id']);
				exit;
			}
			//maj du message
			$reqLog = $bdd->prepare("UPDATE `messages` SET  `contenu` = :message WHERE `id` = :id");
			$reqLog ->execute(array(
				':message' => $_POST['message'],
				':id' => $message['id'],
			)) or die('Erreur maj message : L.'.__LINE__ );
			//retour utilisateur
				$_SESSION['head_msg'] = 'Votre message a bien été modifié.';
				header('Location: topic.php?id='.$message['id_topics']);
				exit;

		}
		elseif(isset($_POST['action']) && $_POST['action'] == 'Supprimer') //suppression
		{
			//Suppression des messages
				$reqMes = $bdd->prepare("DELETE FROM `messages` WHERE `id` = :id");
				$reqMes ->execute(array(
					':id' => $message['id']
				)) or die('Erreur suppression messages : L.'.__LINE__ );
			


			$_SESSION['head_msg'] = 'Le message a bien été supprimé';
			header('Location: topic.php?id='.$message['id_topics']);
			exit;
		}
		elseif(isset($_POST['action']) && $_POST['action'] == 'Annuler') //annulation de suppression
		{

			$_SESSION['head_msg'] = 'Le message n\'a pas été supprimé';
			header('Location: topic.php?id='.$message['id_topics']);
			exit;
		}
		//Formulaires
		elseif(isset($_GET['del']))//Mode suppression
		{

				$titre= 'Suppression du message<br/>';
				$dossier='forum';
				require('../includes/header.php');
				?>
					<h1>Suppression du message</h1>
					<form method="post" action="editmessage.php?id=<?php echo $message['id'];?>">
						Êtes vous sûr de vouloir supprimer le message ?<br/> 
						<input type="submit" name="action" value="Supprimer" class="case">
						<input type="submit" name="action" value="Annuler" class="case">
						
					</form>
				<?php
				require('../includes/footer.php');
		}
		else //Mode modification
		{
				$titre= 'Modification du message';
				$dossier='forum';
				require('../includes/header.php');
				
				?>
					<h1>Modification du message</h1>
					<form method="post" action="editmessage.php?id=<?php echo $message['id'];?>">
						
						<label>Message : <br/>
							<textarea name="message"><?php echo $message['contenu'];?></textarea></label>
									
						<input type="submit" value="Valider" class="case">
						
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
		header("Location: ../index.php");
		exit;
	}
