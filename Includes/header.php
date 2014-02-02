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
		<div class="bandeau"></div>
		<div class="visite">
			<div class="new">Première visite ?
				<a href="inscription.php" class="bouton">Inscription</a>
			</div>	
			<div class="inscrit">Déjà inscrit ?
				<a href="connexion.php" class="bouton">Connexion</a>
				<a href="deconnexion.php" class="bouton">Déconnexion</a>
			</div>
		</div>
		<div class="contenant">
	<?php
		if(!empty($_SESSION['head_msg']))
		{
			echo '<div style="height:50px;background-color:#4c077b;color:white;font-size:24px;text-align:center;margin-top:30px;padding-top:20px;">'.$_SESSION['head_msg'].'</div>';
			$_SESSION['head_msg'] = '';
		}
	echo $user['id'].'</span><br/><br/>';
	?>
	
	<!--'.<span style="color:gray;">Ton id : '-->