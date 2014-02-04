<?php
	//connexion bdd
	require_once('includes/bdd.php');
	//info membre
	require_once('includes/users.php');
	//verif données form reçues
	if(empty($_POST['forum']) || !is_numeric($_POST['forum']) || !isset($_POST['message']) || !isset($_POST['titre']))
	{
		$_SESSION['head_msg'] = 'Désolé, une erreur s\'est produite pendant l\'envoi du topic.';
		$_SESSION['head_class'] = 'erreur';
		header('Location: ./');
		exit;
	}
	//Verif si user connecté
	elseif(!$user['id'])
	{
		$_SESSION['head_msg'] = 'Vous devez être connecté pour poster.';
		$_SESSION['head_class'] = 'erreur';
		header('Location: ./connexion.php');
		exit;
	}
	//Verif longueur message
	elseif(strlen($_POST['message']) < 3)
	{
		$_SESSION['head_msg'] = 'Votre message doit comporter au moins 3 caractères.';
		$_SESSION['head_class'] = 'erreur';
		header('Location: ./forum.php?id='.$_POST['forum']);
		exit;
	}
	//Verif longueur titre
	elseif(strlen($_POST['titre']) < 3 && strlen($_POST['titre']) > 120) 
	{
		$_SESSION['head_msg'] = 'Votre titre doit comporter entre trois et 120 caractères.';
		$_SESSION['head_class'] = 'erreur';
		header('Location: ./forum.php?id='.$_POST['forum']);
		exit;
	}
	//Verif de l'existence du forum
	$reqforum = $bdd->prepare("SELECT `id` FROM `forums` WHERE `id` = :id ");
	$reqforum->execute(array(
		':id' => $_POST['forum']
		)) or die('Erreur requête info forum : L.'.__LINE__ );
	if(!$forum = $reqforum->fetch())
	{
		$_SESSION['head_msg'] = 'Le forum sur lequel vous avez posté n\'existe pas ou plus.';
		$_SESSION['head_class'] = 'erreur';
		header('Location: ./');
		exit;
	}
	//Envoi du message
	$reqLog = $bdd->prepare("INSERT INTO `topics`(`id`, `titre`, `message`, `datepost`, `id_auteur`, `id_forums`) VALUES (NULL,:titre,:message,NOW(),:user,:forum)");
	$reqLog ->execute(array(
		':titre' => $_POST['titre'],
		':message' => $_POST['message'],
		':user' => $user['id'],
		':forum' => $_POST['forum'],
	)) or die('Erreur requête inscription : L.'.__LINE__ );
	//Retour utilisateur
	$_SESSION['head_msg'] = 'Merci pour votre participation, votre topic a été enregistré.';
	header('Location: ./forum.php?id='.$_POST['forum']);
	exit;
?>