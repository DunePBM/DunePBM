<?php 
// Storm Round
// Called from index.php
// setup-round.php --> storm-round.php --> spice-round.php
// colleciton-round.php --> storm-round.php --> spice-round.php
    
//######################################################################
//#### First Run #######################################################
//######################################################################
if (!isset($game['round'])) {
    $game['round'] = array();
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
		$game['meta']['next'][$faction] = 'stormRound';
	}
    dune_writeData('Set up Storm Round', true);
    refreshPage();
}

//######################################################################
//## Every Round #######################################################
//######################################################################
if (isset($game['stormRound'])) {
    //##############################################################
    //## Run if there is no delay. #################################
    //##############################################################
	if (!$game['meta']['delay']['stormRound']) {
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			$game['faction']['alert'][] = 'The storm is in Sector '.$game['storm']['location'].'.';
		}	
		stormAction_endRound();
	}
	
    
    //##############################################################
    //## Checks for end of round. ##################################
    //##############################################################
    $isGameDone = true;
    foreach (array('[A]', '[B]', '[E]','[F]','[G]','[H]') as $faction) {
        if ($game['meta']['next'][$faction] != 'wait') {
            $isGameDone = false;
        }
    }
    if ($isGameDone) {
        stormAction_endRound();
    }
}


//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)){
    global $game, $info;

	echo 
	'<h2>Storm Round</h2>
    <p>The storm is in Sector '.$game['storm']['location'].'.</p>';
    
    if ($game['meta']['turn'] >= 2) {
        echo
        '<p>The storm will move '.$game['storm']['move'].' sectors.</p>';
        'You may play Weather Control or Faimly Atomics.';
    }
    
    echo
    '<br><form action="" method="post">
    <button name="stormAction" value="done">Done with Storm</button>
    </form>';
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (isset($_POST['stormAction'])) {
    if ($_POST['stormAction'] == 'done') {
        dune_readData();
        $game['meta']['next'][$_SESSION['faction']] = 'wait.php';
        dune_writeData('Done with storm-round.');
    }
        
    //##############################################################
    //## Checks for End of Round ###################################
    //##############################################################
    dune_checkRoundEnd('stormRound', 'bidding-round.php', 'Storm round has ended.');
    refreshPage();
}

//######################################################################
//###### Actions #######################################################
//######################################################################
function stormAction_endRound() {
	global $game, $info;
	dune_readData();
	$game['meta']['round'] = 'spice-round.php';
    unset($GLOBALS['game']['round']);
    dune_writeData('Storm Round ends. The Spice Round begins.', true);
    refreshPage();
}
?>
