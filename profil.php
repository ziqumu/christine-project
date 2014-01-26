<label>Présentez vous aux autres utilisateurs</label>

<form method='post' action="profil.php" enctype="multipart/form-data">
<div class='titre' >
	
	<label>Votre Profil </label><br/>
	<label>Pseudo :</label><input name='pseudo' type='text'><br/>
	<label>Avatar :</label><input name='avatar' type='file'>
	<input type="hidden" name="MAX_FILE_SIZE" value="100000"><br/>
	<input name='bouton' type='submit' value='Valider'>
</div>
</form>