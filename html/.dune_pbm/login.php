<?php 
//Login Script
//To be called by index.php.

global $game, $info, $duneForum;

//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)) {
	echo 
	'<h2>Login: </h2>

    <p>This game is currently in debugging mode.</p>

    <p>To log in use the first letter of the faction you wish to
    play as for the name and leave the password blank.</p>
    <p>So, for example, to log in for Atredies, 
    you would log in with . . .<br><br>name: a<br>password: <br></p>

	<form action=\'#\' method="post">
		Name: <input id="name" name="name" type="text" autofocus/> <br>
		Password: <input id="password" name="password" type="text"/> <br>
		<input type="submit" value="Submit">
	</form>';
	
	//## Print Forum ###############################################
	echo
	'<h2>Forum: </h2>';
	for ($i = max(count($duneForum)-10, 0); $i < count($duneForum); $i++) {
        print '<p>'.$duneForum[$i]['faction'].'<br>';
        print $duneForum[$i]['message'].'<br>';
        print $duneForum[$i]['time'].'<br>';
        print '<br></p>';
    }
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (!empty($_POST)) {
    if (isset($_POST['name']) && isset($_POST['password'])) {
        foreach (array_keys($game['meta']['players']) as $x) {
            $tempName = strtolower($_POST['name']);
            $tempPassword = strtolower($_POST['password']);
            
            if (($game['meta']['players'][$x]['name'] == $tempName)
                            && ($game['meta']['players'][$x]['password'] == $tempPassword)) {
                    $_SESSION['faction'] = $x;
            }
        }
    } 
    refreshPage();
}
?>
