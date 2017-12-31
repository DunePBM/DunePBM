<?php 
// Storm Round
// Called from index.php
// setup-round.php --> storm-round.php --> spice-round.php
// colleciton-round.php --> storm-round.php --> spice-round.php

global $game, $info;

//######################################################################
//#### First Run #######################################################
//######################################################################
if (!isset($game['round'])) {
	if ($game['meta']['turn'] == 1) {
		$game['storm']['location'] = $game['storm']['move'];
	}
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
if (isset($game['round'])) {
    
    //##############################################################
    //## Run if there is no delay. #################################
    //##############################################################
	if ($game['meta']['delay']['stormRound'] == false) {
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			$game[$faction]['alert'][] = 'The storm is in Sector '.$game['storm']['location'].'.';
		}
		dune_writeData('Saved storm alerts.', true);
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
        $game['meta']['next'][$_SESSION['faction']] = 'wait';
        dune_writeData($_SESSION['faction'].' is done with storm-round.');
    }
    
    refreshPage();
}

//######################################################################
//###### Actions #######################################################
//######################################################################
function stormAction_moveStorm() {
    global $game, $info;
    /*while ($game['storm']['move'] > 0) {
        $game['storm']['move'] -= 1;
        $game['storm']['location'] += 1;
        if ($game['storm']['location'] == 19) {
            $game['storm']['location'] = 1;
        }
        if (($game['storm']['loation'] -2) % 3 == 0) {
            $game['meta']['playerOrder'] = array_cycle($game['meta']['playerOrder']);
        }
        foreach (array_keys($game['tokens']) as $y) {
            if ($info['territory'][$y]['sector'] == $game['storm']['location']) {
                foreach ($game['tokens'][$y] as $z) {
                    dune_gmMoveTokens($z, 
                                $game['tokens'][$y][$z][0],  
                                $game['tokens'][$y][$z][1],
                                $y, '[TANKS]');
                }
            }
        }
    }*/
}


function stormAction_endRound() {
	global $game, $info;
	dune_readData();
	stormAction_moveStorm();
	$game['meta']['round'] = 'spice-round.php';
    unset($GLOBALS['game']['round']);
    $message = 'The storm is in sector '.$game['storm']['location'].
					' The Storm Round ends. The Spice Round begins.';
    dune_writeData($message, true);
    refreshPage();
}
?>
