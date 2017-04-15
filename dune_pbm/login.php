<?php 
//Login Script
//To be called by index.php.

global $game, $info;

//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)) {
	echo 
	'<h2>Login: </h2>

	<form action=\'#\' method="post">
		Name: <input id="name" name="name" type="text" autofocus/> <br>
		Password: <input id="password" name="password" type="text"/> <br>
		<input type="submit" value="Submit">
	</form>';
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (!empty($_POST)) {
    if (isset($_POST['name']) && isset($_POST['password'])) {
        foreach (array_keys($game['meta']['players']) as $x) {
            $tempName = $_POST['name'];
            $tempPassword = $_POST['password'];
            if (($game['meta']['players'][$x]['name'] == $tempName)
                            && ($game['meta']['players'][$x]['password'] == $tempPassword)) {
                    $_SESSION['faction'] = $x;
            }
        }
    } 
    refreshPage();
}
?>
