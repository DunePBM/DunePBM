<?php 
// The spice round.
// Called by index.php.
// storm-round.php --> spice-round.php --> bidding-round.php

//######################################################################
//## First Run #########################################################
//######################################################################
if (!isset($game['round'])) {
	$game['round'] = array();
	$game['round']['sandworms'] = array();
	foreach (array('[A]','[E]','[F]','[G]','[H]') as $faction) {
		$game['meta']['next'][$faction] = 'wait';
	}
	spiceAction_spiceBlow();
	// End the turn if a nexus didn't occour.
	if ($game['meta']['next'][0] == 'wait') {
		spiceAction_endRound();
	} else {
		dune_writeData('Setup Spice Round.', true);
		refreshPage();
	}
}

//######################################################################
//## Every Round #######################################################
//######################################################################

if (isset($game['round'])) {
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
        spiceAction_endRound();
    }
}


//######################################################################
//###### Forms #########################################################
//######################################################################

if (empty($_POST)){
    global $game, $info;
    
    //##############################################################
    //## Every Run -- Spice Round ##################################
    //##############################################################
	if ($game['spiceRound']['nexus'] == false) {
    }
    echo 
	'<h2>Spice Round</h2>
	<p></p>The storm is in sector '.$game['storm']['location'].'</p>
	<p>Spice Blooms on '.
	$info['spiceDeck'][$game['spiceRound']['spice-1']['location']]['name'].
    ' ('
    .$info['spiceDeck'][$game['spiceRound']['spice-1']['location']]['spice'].
    ') and '
    .$info['spiceDeck'][$game['spiceRound']['spice-2']['location']]['name']
    .' ('
    .$info['spiceDeck'][$game['spiceRound']['spice-2']['location']]['spice'].') ';
    
    echo
    '<br><form action="" method="post">
    <button name="storm_action" value="done">Done with Storm</button>
    </form>';
    
    //##############################################################
    //## Every Run -- Nexus ########################################
    //##############################################################
    if ($game['spiceRound']['nexus'] == true) {
        echo 
		'<h2>Nexus</h2>';

		echo
		'A nexus has occoured. Form alliences.
		The nexus will not end until everyone selects DONE.<br><br>
		There were sandworms in: <br>';
		foreach ($game['nexus']['sandworms'] as $x) {
			print $info['spiceDeck'][$x]['name'].'<br>';
		}
		echo
		'<br><form action="" method="post">
		<button name="nexus_action" value="done">Done with Nexus</button>
		</form>';
	}
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (!empty($_POST)){
    if (isset($_POST['post'])) {
        dune_postForum($_POST['post']);
        refreshPage();
    }
}

//######################################################################
//###### Actions #######################################################
//######################################################################

function spiceAction_spiceBlow() {
    global $game, $info;
    
    // Double spice blow.
    for ($i = 1; $i <= 2; $i += 1) {
        while ($info['spiceDeck'][dune_checkSpice($i, true)]['type'] == 'worm') {
            $underCard = $game['spiceDeck']['discard-'.$i][0];
            array_push($game['round']['sandworms'], $underCard);
            foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
                $game['meta']['next'][$faction] = 'nexus';
            }
            dune_dealSpice($i);
        }
        // Deals spice.
        $game['round']['spice-'.$i]['location'] = dune_checkSpice($i, true);
        $game['round']['spice-'.$i]['spice'] 
                    = $info['spiceDeck'][dune_checkSpice($i, true)]['spice'];
        dune_gmMoveTokens('[SPICE]', $game['spiceRound']['spice-'.$i]['spice'],0,
					'[BANK]', $game['spiceRound']['spice-'.$i]['location']);
        dune_writeData('Spice Card #'.$i, true);
    
    
    }
    if (isset($game['nexus'])) {
        $temp = 'A nexus has occoured. There were sandworms in: ';
        foreach ($game['nexus']['sandworms'] as $x) {
            $temp .= $info['spiceDeck'][$x]['name'];
            $temp .= ', ';
        }
        $temp = substr($temp, 0, -2);
        dune_postForum($temp, true);
    }
}

// If a nexus is occouring.
if (isset($game['nexus'])) {
    if ($game['meta']['next'][$_SESSION['faction']] == 'spice-round.php') {
        dune_getWaiting();
    }
    else {
        refreshPage();
    }
}

// If a nexus does not occour or is finished.
if ((isset($game['spiceRound'])) && (!isset($game['nexus']))) {
    dune_dealSpice(1);
    dune_dealSpice(2);

    /* Fix that spice should not be delt on the storm
        if ($info['spiceDeck'][$game['spiceRound']['spice-1']['location']]['sector'] 
                            != $game['storm']['location'] {
        dune_gmMoveTokens('[SPICE]', (int)$game['spiceRound']['spice-1']['spice'], 
                        0, '[BANK]', $game['spiceRound']['spice-1']['location']);
    }
    if ($info['spiceDeck'][$game['spiceRound']['spice-2']['location']]['sector'] 
                            != $game['storm']['location'] {
        dune_gmMoveTokens('[SPICE]', (int)$game['spiceRound']['spice-2']['spice'], 
                        0, '[BANK]', $game['spiceRound']['spice-2']['location']);
    }*/
    
    $temp = 'Spice Blooms on ';
    $temp .= $info['spiceDeck'][$game['spiceRound']['spice-1']['location']]['name'];
    $temp .= ' ('.$info['spiceDeck'][$game['spiceRound']['spice-1']['location']]['spice'].') and ';
    $temp .= $info['spiceDeck'][$game['spiceRound']['spice-2']['location']]['name'];
    $temp .= ' ('.$info['spiceDeck'][$game['spiceRound']['spice-2']['location']]['spice'].') ';
    dune_postForum($temp, true);
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
		$game[$faction]['alert'][] = $temp;
	}
    unset($game['spiceRound']);
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
                $game['meta']['next'][$faction] = 'storm-round.php';
    }
    dune_writeData();
}

function spiceAction_endRound() {
	global $game, $info;
	dune_readData();
	$game['meta']['round'] = 'spice-round.php';
    unset($GLOBALS['game']['round']);
    dune_writeData('Storm Round ends. The Spice Round begins.', true);
    refreshPage();
}
?>
