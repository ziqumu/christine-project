<?php
	//Verification des parametres
	if (empty($_GET['id']) || !is_numeric ($_GET['id']))
	{
		require_once('includes/users.php');
		$_SESSION['head_msg'] = 'Page non trouvée';
		header("Location: index.php");
		exit;
	}
	//Info forum
	require('includes/bdd.php');
	$reqforum = $bdd->prepare("SELECT `titre` FROM `forums` WHERE `id` = :id ");
	$reqforum->execute(array(
		':id' => $_GET['id']
		)) or die('Erreur requête info forum : L.'.__LINE__ );
	if(!$forum = $reqforum->fetch())
	{
		require_once('includes/users.php');
		$_SESSION['head_msg'] = 'Page non trouvée';
		header("Location: index.php");
		exit;
	}
	//En-tete
	$titre=$forum['titre'];
	require('includes/header.php');
	
	$reqtopic = $bdd->prepare("SELECT t.`id`, t.`titre`, t.`datepost`,u.`login` FROM `topics` AS t JOIN `users` AS u ON t.`id_auteur` = u.`id` WHERE t.`id_forums` = :idforum ORDER BY t.`datepost` DESC,t.`id` DESC");
	$reqtopic->execute(array(
		':idforum' => $_GET['id']
		)) or die('Erreur requête liste forums : L.'.__LINE__ );
		
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

//footer
	require('includes/footer.php');
	
	
?>