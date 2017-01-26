<?php 
	//Header
	echo
	'<form action="#" method="post">
	Actions:  <select name="header_action">
		<option value="status">Get Status</option>			
		
        <option value="logout">Logout</option>			
        <option value="reset">Reset Game</option>			
	</select> 
	<input type="submit" value="Submit">
	</form>';

	if (isset($_POST['header_action'])) {
		if ($_POST['header_action'] == 'logout') {
			session_destroy();
            echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
            //Also Works:
            //$URL="http://yourwebsite.com/";
            //echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
            
		}
        if ($_POST['header_action'] == 'reset') {
			dune_setupGame();
            //echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
            //Also Works:
            //$URL="http://yourwebsite.com/";
            //echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
            
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
