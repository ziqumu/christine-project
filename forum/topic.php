<?php
	//Verification des parametres
	if (empty($_GET['id']) || !is_numeric ($_GET['id']))
	{
		require_once('../includes/users.php');
		$_SESSION['head_msg'] = 'Page non trouvée';
		$_SESSION['head_class'] = 'erreur';
		header("Location: ../");
		exit;
	}
	//Info topic
	require('../includes/bdd.php');
	$reqtopic = $bdd->prepare("SELECT  t.`titre`, t.`datepost`,t.`message`,t.`id_forums`,u.`login`,u.`id` as user_id,u.`avatar`,u.`droit` FROM `topics` AS t JOIN `users` AS u ON t.`id_auteur` = u.`id` WHERE t.`id` = :id ");
	$reqtopic->execute(array(
		':id' => $_GET['id']
		)) or die('Erreur requête info topic : L.'.__LINE__ );
	if(!$topic = $reqtopic->fetch())
	{
		require_once('../includes/users.php');
		$_SESSION['head_class'] = 'erreur';
		$_SESSION['head_msg'] = 'Page non trouvée';
		header("Location: ../");
		exit;
	}

	$reqforum = $bdd->prepare("SELECT  `titre` FROM `forums` WHERE `id` = :id ");
	$reqforum->execute(array(
		':id' => $topic['id_forums']
		)) or die('Erreur requête info forum : L.'.__LINE__ );
	$forum = $reqforum->fetch();
	//En-tete
	$titre=$topic['titre'];
	$dossier  = 'forum';
	require('../includes/header.php');
	echo '<a href="../">Accueil</a> > <a href="./?id='.$topic['id_forums'].'">'.$forum['titre'].'</a> > '.$titre;

	echo '<h1>'.$titre.'</h1>';
	echo '<table><tr>
			<td style="width:220px"><a href="../compte/profil.php?id='.$topic['user_id'].'">';
			
			if(!empty($topic['avatar']))
				echo '<img style="float:left;margin-right:5px;" src="../uploads/'.$topic['user_id'].$topic['avatar'].'" alt="avatar"/>';
			else
				echo '<div style="width:90px;height:70px;border:solid black 1px;float:left;margin-right:5px;text-align:center;padding-top:50px;"> Pas d\'avatar</div>';

			echo $topic['login'].'</a>';

			if($topic['droit'] == 1) echo '<br/>Administrateur';
			elseif($topic['droit'] == -1) echo '<br/>Banni';


			echo'<br/>'.$topic['datepost'] . '</td>
			<td id="mfirst" style="word-wrap: break-word;max-width: 555px;">'. nl2br(htmlspecialchars($topic['message']));

			if($user['id'] != false && ($topic['user_id'] == $user['id'] || $user['droit'] == 1)) 
			echo'
			<div class="modsuppr"><a href="edittopic.php?id='.$_GET['id'].'" class="bouton" > modifier</a>
					<a href="edittopic.php?id='.$_GET['id'].'&del" class="bouton" >Supprimer</a></div>';

			echo '</td>
		</tr></table>';
	
	$reqmessage = $bdd->prepare("SELECT u.`login`,m.`id`,m.`contenu`,m.`datepost`,m.`id_users`,u.`login`,u.`id` as user_id,u.`avatar`,u.`droit`
								FROM `messages` AS m 
								JOIN users AS u
								ON m.`id_users`= u.`id`
								WHERE m.`id_topics`= :idtopic
								ORDER BY m.`datepost` DESC");
	$reqmessage->execute(array(
		':idtopic' => $_GET['id']
		)) or die('Erreur requête liste forums : L.'.__LINE__ );
		
	echo '<h2>Réponses</h2><table style="background-color:#bdbdbd;">';
		while($message = $reqmessage->fetch())
	{
			echo '<tr style="background-color:#efefef;">
					<td style="width:220px"><a href="../compte/profil.php?id='.$message['user_id'].'">';
			
			if(!empty($message['avatar']))
				echo '<img style="float:left;margin-right:5px;" src="../uploads/'.$message['user_id'].$message['avatar'].'" alt="avatar"/>';
			else
				echo '<div style="width:90px;height:70px;border:solid black 1px;float:left;margin-right:5px;text-align:center;padding-top:50px;"> Pas d\'avatar</div>';

			echo $message['login'].'</a>';

			if($message['droit'] == 1) echo '<br/>Administrateur';
			elseif($message['droit'] == -1) echo '<br/>Banni';


			echo'<br/>'.$topic['datepost'] . '</td>
			<td id="mfirst" style="word-wrap: break-word;max-width: 555px;">'. nl2br(htmlspecialchars($message['contenu'])) ;

			if($user['id'] != false && ($message['user_id'] == $user['id'] || $user['droit'] == 1)) 
			echo'
			<div class="modsuppr"><a href="editmessage.php?id='.$message['id'].'" class="bouton" > modifier</a>
					<a href="editmessage.php?id='.$message['id'].'&del" class="bouton" >Supprimer</a></div>';

			echo '</td>
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
		echo 'Vous devez être <a href="../compte/inscription.php">inscrit</a> et <a href="../compte/connexion.php">connecté</a> pour répondre.';
	}
//footer
	require('../includes/footer.php');
	
	
?>