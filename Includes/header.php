<?php
	//connexion bdd
		require_once(__DIR__.'/bdd.php');
	//info membre
		require_once(__DIR__.'/users.php');

	if(!empty($dossier))$path = '../';
	else $path = '';
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title><?php echo $titre;?> &middot; Savoir-Faire Maison </title>
		<link rel="stylesheet" type="text/css" href="<?php echo $path;?>style.css">
	</head>
	<body>
		<a class="bandeau" href="<?php echo $path;?>./"></a><div class="visite">
	<?php 
		if($user['id']) //Si user connecté
		{
			?>
					Bonjour <?php echo $user['login'];?> - <a href="<?php echo $path;?>compte/profil.php?id=<?php echo $user['id'];?>" class="bouton">Mon profil</a> 
					<a href="<?php echo $path;?>compte/deconnexion.php" class="bouton">Déconnexion</a>
			<?php
		}
		else
		{
			?>	
				<div class="new">Première visite ?
					<a href="<?php echo $path;?>compte/inscription.php" class="bouton">Inscription</a>
				</div>	
				<div class="inscrit">Déjà inscrit ?
					<a href="<?php echo $path;?>compte/connexion.php" class="bouton">Connexion</a>
				</div>
			<?php
		}
		echo '</div><div class="contenant">';
		if(!empty($_SESSION['head_msg']))
		{
			if(!isset($_SESSION['head_class']))$_SESSION['head_class'] = '';
			echo '<div class="notif '.$_SESSION['head_class'].'" >'.$_SESSION['head_msg'].'</div>';
			$_SESSION['head_msg'] = '';
			$_SESSION['head_class'] = '';
		}
	echo '<br/>';
	?>
	