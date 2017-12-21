<?php 
// Revealed Forum Posts
// Called from index.php
// uses $_SESSION['override']

global $game, $info;

//######################################################################
//###### Every Time ####################################################
//######################################################################

//## Checks to see if posts are finished. ########################
//## Post to forum if they are. ##################################
if (!empty($game['revealedPost'])) {
	foreach ($game['revealedPost'] as $message) {
		if (!in_array('', $message)) {
			$text = 'Revealed Post:<br>';
			foreach (array_keys($message) as $x) {
				$text .= 'From: '.$x.': ';
				$text .= $message[$x].'<br>';
			}
			unset($message);
			dune_writeData('Revealed Forum Post:'.$text, true);
			dune_postFoum($text, true);
		}
	}
}

//######################################################################
//###### Forms #########################################################
//######################################################################

if (empty($_POST)){
    global $game, $info;
    
    if (!empty($game['revealedPost'])) {
		for ($i = 0; $i <= count($game['revealedPost']); $i++) {
			$message = $game['revealedPost'][$i];
			$textParties = 
			if (array_key_exists($_SESSION['faction'], $message)) {
				echo
				'Message to be revealed between '.array_keys($message).':<br>
				Current message: '.$message[$_SESSION['faction']].'<br><br>';
				
				echo
				'<form action="" method="post">
				Post revealed message betweeen '.array_keys($message).':<br>
				<input type="textarea" name="replyRevealedPost-'.$i.'">
				<input type="submit" value="Submit">
				</form> <br><br>';
			}
		}
	}
    echo'
    Send a Revealed Message<br>
    The text of this message will not be revealed until all parties have
    submitted a message.<br><br>
    <form action="" method="post">
    With: <select name="withFaction">';
    
    foreach (array_diff(array('[A]','[B]','[E]','[F]','[G]','[H]'), array($_SESSION['faction'])) as $faction) {
		echo '
        <option value="'.$faction.'">'.$info['factions'][$faction]['name'].'</option>';
    }
    echo '
    </select><br>
    Post:<br>
    <input type="textarea" name="sendRevealedPost">
    <input type="submit" value="Submit">
    </form> ';
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (!empty($_POST)){
    global $game, $info;
    for ($i = 0; $i <= count($game['revealedPost']); $i++) {
        if (isset($_POST['replyRevealedPost-'.$i])) {
            $game['revealedPost'][$i][$_SESSION['faction']] 
                        = $_POST['replyRevealedMessage-'.$i];
            dune_writeData("Revealed Post replied.");
            refreshPage();
        }
    }
    
    if (isset($_POST['sendRevealedPost'])) {
        $game['revealedPost'][] = array($_SESSION['faction'] => $_POST['sendRevealedPost'], 
                                        $_POST['withFaction'] => '');
        dune_writeData("Revealed Post sent.");
        refreshPage();
    }
}
            
//######################################################################
//###### Actions #######################################################
//######################################################################
?>
