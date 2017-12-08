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

//######################################################################
//###### Forms #########################################################
//######################################################################

if (empty($_POST)){
    global $game, $info;
    
    for ($i = 0; $i <= size_of($game['revealedMessages']); $i++) {
        $message = $game['revealedMessages'][$i];
        if (array_key_exists($SESSION['faction'], $message) {
            echo
            'Message to be revealed between '.array_keys($message).':<br>
            Current message: '.$message[$SESSION['faction']].'<br><br>';
            
            echo
            '<form action="" method="post">
            Post revealed message betweeen '.array_keys($message).':<br>
            <input type="textarea" name="replyRevealedPost-'.$i.'">
            <input type="submit" value="Submit">
            </form> ';
        }
    }
    echo
    'Send a Revealed Message<br>
    The text of this message will not be revealed until all parties have
    submitted a message.<br>
    <form action="" method="post">
    With: <select name="withFaction">
        <option value="[A]">Atreides</option>
        <option value="[B]">Bene Gesserit</option>
        <option value="[E]">Emperor</option>
        <option value="[F]">Fremen</option>
        <option value="[G]">Guild</option>
        <option value="[H]">Harkonnen</option>
        </select><br>
    <br>
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
    for ($i = 0; $i <= size_of($game['revealedPost']); $i++) {
        if (isset($_POST['completeRevealedMessage-'.$i])) {
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
