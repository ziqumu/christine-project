<?php
	$dossier = 'forum';
	//Verification des parametres
	if (empty($_GET['id']) || !is_numeric ($_GET['id']))
	{
		require_once('../includes/users.php');
		$_SESSION['head_msg'] = 'Page non trouvée';
		header("Location: ../");
		exit;
	}
	//Info forum
	require('../includes/bdd.php');
	$reqforum = $bdd->prepare("SELECT `titre` FROM `forums` WHERE `id` = :id ");
	$reqforum->execute(array(
		':id' => $_GET['id']
		)) or die('Erreur requête info forum : L.'.__LINE__ );
	if(!$forum = $reqforum->fetch())
	{
		require_once('../includes/users.php');
		$_SESSION['head_msg'] = 'Page non trouvée';
		header("Location: ../");
		exit;
	}
	//En-tete
	$titre=$forum['titre'];
	require('../includes/header.php');
	
	$reqtopic = $bdd->prepare("SELECT t.`id`, t.`titre`, t.`datepost`,u.`login` FROM `topics` AS t JOIN `users` AS u ON t.`id_auteur` = u.`id` WHERE t.`id_forums` = :idforum ORDER BY t.`datepost` DESC,t.`id` DESC");
	$reqtopic->execute(array(
		':idforum' => $_GET['id']
		)) or die('Erreur requête liste forums : L.'.__LINE__ );
		
	echo '<a href="../">Accueil</a> > '.$titre;
	echo '<h1>'.$titre.'</h1>';

	echo '<table>';
		while($topic = $reqtopic->fetch())
	{
			echo '<tr>
					<td><a href="topic.php?id='.$topic['id'].'">'. $topic['titre'] . '</a></td>
					<td>'. $topic['datepost'] . '</td>
					<td>'. $topic['login'] . '</td>
				</tr>';

	}
	echo '</table>';
//Poster un nouveau topic
	echo '<h2>Poster un nouveau topic</h2>';

	if($user['id'] != false) //Si membre
	{
		?>
			<form method="post" action="posttopic.php">
				<input type="hidden" name="forum" value="<?php echo $_GET['id'];?>"/>
				<label> Votre Titre : <br/>
				<input type="text" name="titre"/></label><br/>
				<label> Votre message : <br/>
				<textarea name="message"></textarea></label><br/>
				<input type="submit" value="envoyer"/>
			</form>
		<?php
	}
	else
	{
		echo 'Vous devez être <a href="../compte/inscription.php">inscrit</a> et <a href="../compte/connexion.php">connecté</a> pour poster un nouveau topic.';
	}
//footer
	require('../includes/footer.php');
?>