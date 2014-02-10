<?php
	$titre='Profil';
	$dossier = 'compte';
	require_once('../includes/users.php');
	require('../includes/header.php');

	if(!isset($_GET['id']) || !is_numeric($_GET['id']))
	{
		$_SESSION['head_msg'] = 'Page non trouvée';
		$_SESSION['head_class'] = 'erreur';
		header("Location: ../");
		exit;
	}

	//Info user
	require('../includes/bdd.php');
	$requser = $bdd->prepare("SELECT `id`, `login`, `mdp`, `email`, `droit`, `sexe`, 
		DATE_FORMAT(`datenais`,'le %d.%m.%Y') as `datenais`, 
		`region`, 
		DATE_FORMAT(`inscription`,'le %d.%m.%Y à %H:%i:%s') as `inscription`, 
		DATE_FORMAT(`lastconnexion`,'le %d.%m.%Y à %H:%i:%s') as `lastconnexion`, `ip`, `avatar` FROM `users` WHERE `id` = :id ");
	$requser->execute(array(
		':id' => $_GET['id']
		)) or die('Erreur requête info user : L.'.__LINE__ );
	if(!$membre = $requser->fetch())
	{
		$_SESSION['head_msg'] = 'Page non trouvée';
		$_SESSION['head_class'] = 'erreur';
		header("Location: ../");
		exit;
	}

	if($user['id'] != false && $user['droit'] == 1) // Si admin
		echo '<a href="liste.php">Liste des membres</a>';



echo '
	<h1>Profil de '.$membre['login'].'</h1>';
	//Avatar
		if(!empty($membre['avatar']))
			echo '<img style="float:right;" src="../uploads/'.$membre['id'].$membre['avatar'].'"" alt="avatar"/>';
	echo '<h3>Infos générales</h3>';
	//Ban ou admin
		if($user['id'] != false && $membre['droit'] == 1)
			echo '<strong>Il est administrateur</strong><br/>';
		elseif($user['id'] != false && $membre['droit'] == -1)
			echo '<strong>Il est banni</strong><br/>';
	echo 'Inscription : '.$membre['inscription'];
	echo '<br/>Dernière connexion : '.$membre['lastconnexion'];

	echo '<h3>Infos personnelles</h3>';
		if($membre['sexe'] == 0)
			echo 'Homme<br/>';
		elseif($membre['sexe'] == 1)
			echo 'Femme<br/>';
	if($membre['datenais'] !=  'le 0.00.0000')
		echo 'Date de naissance : '.$membre['datenais'].'<br/>';
	if(!empty($membre['region']))
		echo 'Region : '.$membre['region'];

	if($user['id'] != false && ($membre['id'] == $user['id'] || $user['droit'] == 1)) // Si admin ou auteur
	{
		echo '<h3>Infos privées</h3>';
		echo 'IP : '.$membre['ip'];
		echo '<br/>Email : '.$membre['email'];
		echo '<br/><br/><a href="editprofil.php?id='.$membre['id'].'">Modifier le profil</a>';
	}

	if($user['id'] != false && $user['droit'] == 1) // Si admin
	{
		if($membre['droit'] != 0)
			echo '<br/><a href="changedroit.php?value=0&id='.$membre['id'].'">Rétablir comme membre normal sans droit</a>';
		else
			echo '<br/><a href="changedroit.php?value=-1&id='.$membre['id'].'">Bannir</a> - <a href="changedroit.php?value=1&id='.$membre['id'].'">Rendre admin</a>';
	}
	?>

<?php
require('../includes/footer.php');
?>