<?php 
// Collection Round
// Called from index.php
// battle-round.php --> colleciton-round.php --> spice-round.php

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
    
    // First run:
    if (!isset($game['collectionRound'])) {
        $game['collectionRound'] = array();
    }
    
    // Every run:
	echo 
	'<h2>Collection Round</h2>';
    
    echo
    '<br><form action="" method="post">
    <button name="storm_action" value="done">Done with Storm</button>
    </form>';
}

if (isset($_POST['collection_action'])) {
    if ($_POST['movement_action'] == 'done') {
        dune_readData();
        $game['meta']['next'][$_SESSION['faction']] = 'wait.php';
    }
    
    // Checks for end of round.
    dune_checkRoundEnd('biddingRound', 'movement-round.php');
    dune_writeData();
    refreshPage();
    }        
}
?>
