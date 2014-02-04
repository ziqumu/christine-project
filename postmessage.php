<?php
	//connexion bdd
	require_once('includes/bdd.php');
	//info membre
	require_once('includes/users.php');
	//verif données form reçues
	if(empty($_POST['topic']) || !is_numeric($_POST['topic']) || !isset($_POST['message']))
	{
		$_SESSION['head_msg'] = 'Désolé, une erreur s\'est produite pendant l\'envoi du message.';
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
		$_SESSION['head_msg'] = 'Votre message doit comporter au moins trois caractères.';
		$_SESSION['head_class'] = 'erreur';
		header('Location: ./topic.php?id='.$_POST['topic']);
		exit;
	}
	//Verif de l'existence du topic
	$reqtopic = $bdd->prepare("SELECT `id` FROM `topics` WHERE `id` = :id ");
	$reqtopic->execute(array(
		':id' => $_POST['topic']
		)) or die('Erreur requête info topic : L.'.__LINE__ );
	if(!$topic = $reqtopic->fetch())
	{
		$_SESSION['head_msg'] = 'Le topic sur lequel vous avez posté n\'existe pas ou plus.';
		$_SESSION['head_class'] = 'erreur';
		header('Location: ./');
		exit;
	}
	//Envoi du message
	$reqLog = $bdd->prepare("INSERT INTO `messages`(`id`, `contenu`, `datepost`, `id_users`, `id_topics`) VALUES (NULL,:message,NOW(),:user,:topic)");
	$reqLog ->execute(array(
		':message' => $_POST['message'],
		':user' => $user['id'],
		':topic' => $_POST['topic'],
	)) or die('Erreur requête inscription : L.'.__LINE__ );
	//Retour utilisateur
	$_SESSION['head_msg'] = 'Merci pour votre participation, votre message a été enregistré.';
	header('Location: ./topic.php?id='.$_POST['topic']);
	exit;
?>