<?php 
// Battle Round
// Called from index.php
// movement-round.php --> battle-round.php --> colleciton-round.php

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
    
    // First run:
    if (!isset($game['battleRound'])) {
        $game['battleRound'] = array();
    }
    
    // Every run:
	echo 
	'<h2>Battle Round</h2>';
}

if (isset($_POST['battle_action'])) {
    if ($_POST['battle_action'] == 'done') {
        dune_readData();
        $game['meta']['next'][$_SESSION['faction']] = 'wait.php';
        dune_writeData('Done with battle round.');
    }
    
    // Checks for end of round.
    dune_readData();
    dune_checkRoundEnd('battleRound', 'collection-round.php');
    dune_writeData('Battle round has ended.', true);
    refreshPage();
    }        
}
?>
