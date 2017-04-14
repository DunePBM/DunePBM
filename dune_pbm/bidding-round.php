<?php 
// Bidding Round
// Called from index.php
// storm-round.php --> bidding-round.php --> movement-setup.php

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
    
    // First run:
    if (!isset($game['biddingRound'])) {
        $game['stormRound'] = array();
    }
    
    // Every run:
	echo 
	'<h2>Bidding Round</h2>';
}

if (isset($_POST['storm_action'])) {
    if ($_POST['storm_action'] == 'done') {
        dune_readData();
        $game['meta']['next'][$_SESSION['faction']] = 'wait.php';
        
        // Checks for end of round.
        dune_checkRoundEnd('biddingRound', 'movement-round.php');
        dune_writeData();
        refreshPage();
    }        
}
?>
