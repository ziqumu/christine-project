<?php
	$titre='contact';
	require('includes/header.php');
?>	

<h1>Nous contacter</h1>
	
	<form method='post' action=""mailto:christine.job7@gmail.com" enctype="text/plain">

		<label>Nom:</label> <div><input name="nom"></div>
		<label>Prénom: <div><input name="prenom" ></div></label>
		<label>Adresse email où vous recevrez notre réponse :<br/><div><input type="email" name="email" size="50px"></div></label><br/>
		<label>Votre Message: <textarea name="message" rows=5 cols=30></textarea></label>

        <input type="submit" value="Envoyer">
        <input type="reset" value="Effacer">

	</form>	
		

<!--captcha>

<?php
	require('includes/footer.php');
?>