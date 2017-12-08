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
        dune_postFoum($text);
    }
}

//######################################################################
//###### Forms #########################################################
//######################################################################

if (empty($_POST)){
    global $game, $info;
    
    foreach ($game['revealedMessages'] as $message) {
        if (array_key_exists($SESSION['faction'], $message) {
            echo
            'Revealed message between '.array_keys($message).':<br>
            Message: '.$message[$SESSION['faction']].'<br><br>';
            
            echo
            '<form action="" method="post">
            Post revealed message betweeen '.array_keys($message).':<br>
            <input type="textarea" name="completeRevealedMessage">
            <input type="submit" value="Submit">
            </form> ';
        }
    }
    echo
    'Send a Revealed Message<br>
    The text of this message will not be revealed until all parties have
    submitted a message.<br>
    <form action="" method="post">
    To: <select name="toFaction">
        <option value="[A]">Atreides</option>
        <option value="[B]">Bene Gesserit</option>
        <option value="[E]">Emperor</option>
        <option value="[F]">Fremen</option>
        <option value="[G]">Guild</option>
        <option value="[H]">Harkonnen</option>
        </select><br>
    <br>
    Post:<br>
    <input type="textarea" name="sendRevealedMessage">
    <input type="submit" value="Submit">
    </form> ';
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (!empty($_POST)){
    if (isset($_POST['completeRevealedMessage'])) {
        
        refreshPage();
    }
    
    
    if (isset($_POST['sendRevealedMessage'])) {
        
        refreshPage();
    }
}

dune_printStatus($_SESSION['faction']);
print '<br><hr>';
if ($game['meta']['next'][$_SESSION['faction']] != 'wait') {
    dune_getWaiting();
}
            
//######################################################################
//###### Actions #######################################################
//######################################################################

?>
