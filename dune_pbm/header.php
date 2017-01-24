<?php 
	//Header
	echo
	'<form action="#" method="post">
	Actions:  <select name="header_action">
		<option value="status">Get Status</option>			
		<option value="logout">Logout</option>			
	</select> 
	<input type="submit" value="Submit">
	</form>';

	if (isset($_POST['header_action'])) {
		if ($_POST['header_action'] == 'logout') {
			session_destroy();
			header('Location: index.php');
		}
		if ($_POST['header_action'] == 'status') {
			print 'Game Status:<br>';
			global $game;
			print '<pre>';
			print json_encode($game, JSON_PRETTY_PRINT);
			print '</pre>';
		}
	}

?>
