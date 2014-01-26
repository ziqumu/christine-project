

<h2><?php echo $titre; ?></h2>
<form method="post" action="poster.php">
	<?php 
	if($isTopic)
	{
		echo '<label> Nouveau Topic : <br/> <input type="text" name="title"/></label><br/>';
	}
	else
	{
		echo'<input type="hidden" name="topic" value="'.$idTopic.'"/>';
	}
	?>
	<label> Votre message : <br/>
	<textarea name="message"></textarea></label>
	<input type="submit" value="envoyer"/>
	
</form>