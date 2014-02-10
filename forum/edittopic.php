<?php
	
	if(isset($_GET['id']) && is_numeric($_GET['id'])) //Demande de modification
	{
		require('../includes/bdd.php');
		//recup info topic
		$reqtopic = $bdd->prepare("SELECT  `id`, `titre`, `datepost`,`message`, `id_auteur` FROM `topics` WHERE `id` = :id ");
		$reqtopic->execute(array(
			':id' => $_GET['id']
			)) or die('Erreur requête info topic : L.'.__LINE__ );

		//Verif de l'existence du topic
		if(!$topic = $reqtopic->fetch())
		{
			require_once('../includes/users.php');
			$_SESSION['head_msg'] = 'Le topic que vous avez essayé de modifier n\'existe pas ou plus';
			header("Location: ../");
			exit;
		}

		//Verif des droits
		require_once('../includes/users.php');
		if($user['id'] == false || ($topic['id_auteur'] != $user['id'] && $user['droit'] != 1)) //Si il n'est pas connecté ou (ce n'est pas l'auteur du topic et qu'il n'est pas admin)
		{
			$_SESSION['head_msg'] = 'Vous n\'avez pas le droit de modifier ce topic';
			header("Location: topic.php?id=".$topic['id']);
			exit;
		}

		//Execution des actions
		if(isset($_POST['titre']) && isset($_POST['message'])) //Execution modification message
		{
			//Verif longueur message
			if(strlen($_POST['message']) < 3)
			{
				$_SESSION['head_msg'] = 'Votre message doit comporter au moins 3 caractères.';
				$_SESSION['head_class'] = 'erreur';
				header('Location: edittopic.php?id='.$topic['id']);
				exit;
			}
			//Verif longueur titre
			elseif(strlen($_POST['titre']) < 3 || strlen($_POST['titre']) > 120) 
			{
				$_SESSION['head_msg'] = 'Votre titre doit comporter entre 3 et 120 caractères.';
				$_SESSION['head_class'] = 'erreur';
				header('Location: edittopic.php?id='.$topic['id']);
				exit;
			}
			//maj du message
			$reqLog = $bdd->prepare("UPDATE `topics` SET `titre` = :titre, `message` = :message WHERE `id` = :id");
			$reqLog ->execute(array(
				':titre' => $_POST['titre'],
				':message' => $_POST['message'],
				':id' => $topic['id'],
			)) or die('Erreur requête inscription : L.'.__LINE__ );
			//retour utilisateur
				$_SESSION['head_msg'] = 'Votre topic a bien été modifié.';
				header('Location: topic.php?id='.$topic['id']);
				exit;


		}
		elseif(isset($_POST['action']) && $_POST['action'] == 'Supprimer') //suppression
		{
			//Suppression des messages
				$reqMes = $bdd->prepare("DELETE FROM `messages` WHERE `id_topics` = :id");
				$reqMes ->execute(array(
					':id' => $topic['id']
				)) or die('Erreur suppression messages : L.'.__LINE__ );

			//Suppression du topic
				$reqMes = $bdd->prepare("DELETE FROM `topics` WHERE `id`=:id");
				$reqMes ->execute(array(
					':id' => $topic['id']
				)) or die('Erreur suppression topic : L.'.__LINE__ );
			


			$_SESSION['head_msg'] = 'Le topic a bien été supprimé';
			header("Location: ../");
			exit;
		}
		elseif(isset($_POST['action']) && $_POST['action'] == 'Annuler') //annulation de suppression
		{

			$_SESSION['head_msg'] = 'Le topic n\'a pas été supprimé';
			header("Location: topic.php?id=".$topic['id']);
			exit;
		}
		//Formulaires
		elseif(isset($_GET['del']))//Mode suppression
		{

				$titre= 'Suppression du topic : ' . $topic['titre'];
				$dossier='forum';
				require('../includes/header.php');
				
				?>
					<h1>Suppression du topic</h1>
					<form method="post" action="edittopic.php?id=<?php echo $topic['id'];?>">
						Êtes vous sûr de vouloir supprimer le topic suivant ?<br/> 
							<?php echo $topic['titre'];?><br/>
						<input type="submit" name="action" value="Supprimer" class="case">
						<input type="submit" name="action" value="Annuler" class="case">
						
					</form>
				<?php
				require('../includes/footer.php');
		}
		else //Mode modification
		{
				$titre= 'Modification du topic : ' . $topic['titre'];
				$dossier='forum';
				require('../includes/header.php');
				
				?>
					<h1>Modification du topic</h1>
					<form method="post" action="edittopic.php?id=<?php echo $topic['id'];?>">
						
						<label>Titre : <br/>
							<input type="text" name="titre" required value="<?php echo $topic['titre'];?>"></label><br/>
						<label>Message : <br/>
							<textarea name="message"><?php echo $topic['message'];?></textarea></label>
									
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
