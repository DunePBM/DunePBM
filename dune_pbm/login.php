<?php 
//Login Script
//To be called by index.php.

if (!empty($_POST)) {
	$_SESSION['faction'] = $_POST['faction'];
	//echo $_SESSION['faction'];
	header('Location: index.php');
} 

else {
	echo 
	'<h2>Login: </h2>

	<form action=\'#\' method="post">
		Name: <input id="name" name="name" type="text"/> <br>
		Password: <input id="name" name="name" type="text"/> <br>
		Faction:  <select name="faction">
			<option value="[A]">Atredies</option>
			<option value="[B]">Bene Gesserit</option>
			<option value="[E]">Emperor</option>
			<option value="[F]">Fremen</option>
			<option value="[G]">Guild</option>
			<option value="[H]">Harkonnen</option>
		</select> 
		<input type="submit" value="Submit">
	</form>';
}
?>
