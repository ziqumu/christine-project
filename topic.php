<?php
	//Verification des parametres
	if (empty($_GET['id']) || !is_numeric ($_GET['id']))
	{
		require_once('includes/users.php');
		$_SESSION['head_msg'] = 'Page non trouvée';
		header("Location: index.php");
		exit;
	}
	//Info topic
	require('includes/bdd.php');
	$reqtopic = $bdd->prepare("SELECT  t.`titre`, t.`datepost`,t.`message`,u.`login` FROM `topics` AS t JOIN `users` AS u ON t.`id_auteur` = u.`id` WHERE t.`id` = :id ");
	$reqtopic->execute(array(
		':id' => $_GET['id']
		)) or die('Erreur requête info topic : L.'.__LINE__ );
	if(!$topic = $reqtopic->fetch())
	{
		require_once('includes/users.php');
		$_SESSION['head_msg'] = 'Page non trouvée';
		header("Location: index.php");
		exit;
	}
		
	//En-tete
	$titre=$topic['titre'];
	
	require('includes/header.php');
	echo '<h1>'.$titre.'</h1>';
	echo $topic['datepost'].'<br/>'.$topic['login'].'<br/>'.$topic['message'] ;
	
	$reqmessage = $bdd->prepare("SELECT u.`login`,m.`id`,m.`contenu`,m.`datepost`,m.`id_users` 
								FROM `messages` AS m 
								JOIN users AS u
								ON m.`id_users`= u.`id`
								WHERE m.`id_topics`= :idtopic
								ORDER BY m.`datepost` ASC");
	$reqmessage->execute(array(
		':idtopic' => $_GET['id']
		)) or die('Erreur requête liste forums : L.'.__LINE__ );
		
	echo '<table>';
		while($message = $reqmessage->fetch())
	{
			echo '<tr>
					<td>'. $message['login'].'<br/>'.$message['datepost'] . '</td>
					<td id="m'.$message['id'].'">'. $message['contenu'] . '</td>
				</tr>';
	}
		echo '	</table>
				<h2>Répondre</h2>';

	if($user['id'] != false) //Si membre
	{
		?>
			<form method="post" action="postmessage.php">
				<input type="hidden" name="topic" value="<?php echo $_GET['id'];?>"/>
				<label> Votre message : <br/>
				<textarea name="message"></textarea></label><br/>
				<input type="submit" value="envoyer"/>
			</form>
		<?php
	}
	else
	{
		echo 'Vous devez être <a href="inscription.php">inscrit</a> et <a href="connexion.php">connecté</a> pour répondre.';
	}
//footer
	require('includes/footer.php');
	
	
?>