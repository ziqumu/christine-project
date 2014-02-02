<?php
	$titre='Accueil';
	require('includes/header.php');
	


	$reqforum = $bdd->prepare("SELECT f.`id`, c.`nom` as `cat`,f.`titre`,f.`descript`
	FROM `categories` AS c 
	LEFT JOIN `forums` AS f 
	ON c.`id` = f.`id_categorie`
	ORDER BY c.`ordre`,f.`ordre` ASC");
	$reqforum->execute() or die('Erreur requête liste forums : L.'.__LINE__ . $reqforum->errorInfo()[2]);
	$catlast = false;
	while($forum = $reqforum->fetch())
	{
		if ($catlast != $forum['cat'] ) 
		{
			if($catlast !== false)
			{
				echo '</table>';
			}
			echo '<h3>'. $forum['cat'] . '</h3>
				<table> ';
		}
		$catlast = $forum['cat'] ;	 
	
			echo '<tr>
					<td class="liens"><a href="forum.php?id='.$forum['id'].'">'. $forum['titre'] . '</a></td>
					<td class="descript">'. $forum['descript'] . '</td>
				</tr>';
	}
	echo'</table>';
/*	//Test admin
	if($user['id'] == 0)
	{
		echo 'Bonjour voyageur !';
	}
	elseif ($user['droit']==1)
	{
		echo "Bienvenue votre majesté ".$user['login']. ' !';
	}
	else
	{
		echo "Bonjour ".$user['login']. ' !';
	}
	*/
//footer
	require('includes/footer.php');
	
	
?>
