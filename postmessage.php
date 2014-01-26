<?php
if (empty($_POST['message']))
{
	$isTopic = isset($_GET['topic']);
	if($isTopic)
	{
		$titre = "Créer un nouveau topic" ;
	}
	else
	{
		$titre = "Répondre" ;
	}

//En-tete
	require('includes/header.php');
	require('includes/poster.php'); 

//footer
	require('includes/footer.php');
}

else


?>