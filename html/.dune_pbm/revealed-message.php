<?php 
// Revealed Messages
// Called from index.php
// uses $_SESSION['override']

//######################################################################
//###### Forms #########################################################
//######################################################################

if (empty($_POST)){
    foreach ($game['revealedMessage'] as $message) {
        if (array_key_exists($SESSION['faction'], $message) {
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
        dune_postForum($_POST['post']);
        refreshPage();
    }
    
    
    if (isset($_POST['sendRevealedMessage'])) {
        dune_postForum($_POST['post']);
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