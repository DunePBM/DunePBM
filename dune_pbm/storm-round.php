<?php 
// Storm Round
// Called from index.php
// setup-treachery.php --> storm-round.php --> spice-round.php
// colleciton-round.php --> storm-round.php --> spice-round.php

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
    
    // First run:
    if (!isset($game['stormRound'])) {
        $game['stormRound'] = array();
    }
    
    // Every run:
	echo 
	'<h2>Storm Round</h2>
    <p>The storm is in Sector '.$game['storm']['location'].'.</p>
    <p>The storm will move '.$game['storm']['move'].' sectors.</p><br>';
 
    if ($game['meta']['turn'] >= 2) {
        echo
        'You may play Weather Control or Faimly Atomics.';
    }
    
    echo
    '<br><form action="" method="post">
    <button name="storm_action" value="done">Done with Storm</button>
    </form>';
}

if (isset($_POST['storm_action'])) {
    if ($_POST['storm_action'] == 'done') {
        dune_readData();
        $game['meta']['next'][$_SESSION['faction']] = 'wait.php';
        dune_writeData('Done with storm-round.');
    }
        
    // Checks for end of round.
    dune_checkRoundEnd('stormRound', 'bidding-round.php');
    dune_writeData('Storm round has ended.', true);
    refreshPage();
}
?>
