<?php
	//connexion bdd
		require_once('includes/bdd.php');
	//info membre
		require_once('includes/users.php');
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title><?php echo $titre;?> &middot; Savoir-Faire Maison </title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<a href="inscription.php">Inscription</a>
		<a href="connexion.php">Connexion</a>
		<a href="deconnexion.php">Deconnexion</a><br/>
	<?php
		if(!empty($_SESSION['head_msg']))
		{
			echo '<div style="height:30px;background-color:green;color:white;">'.$_SESSION['head_msg'].'</div>';
			$_SESSION['head_msg'] = '';
		}
	echo '<span style="color:gray;">Ton id : '.$user['id'].'</span><br/><br/>';
	?>
	
	