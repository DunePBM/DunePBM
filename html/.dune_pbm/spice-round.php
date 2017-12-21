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
	$game['round']['freeSandworms'] = 0;
	$game['round']['spice-1'] = array();
	$game['round']['spice-2'] = array();
	$game['round']['spice-1']['spice'] = 0;
	$game['round']['spice-2']['spice'] = 0;
	$game['round']['spice-1']['location'] = '';
	$game['round']['spice-2']['location'] = '';
	$game['round']['nexus'] = false;
	foreach (array('[A]','[E]','[F]','[G]','[H]') as $faction) {
		$game['meta']['next'][$faction] = 'wait';
	}
	spiceAction_spiceBlow();
	dune_writeData('Setup Spice Round', true);
	
	// End the turn if a nexus didn't occour.
	if ($game['round']['nexus'] == false) {
		spiceAction_endRound();
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
        if ($game['meta']['next'][$faction] == 'nexus') {
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
    //## This only gets run if there is a Nexus ####################
    //##############################################################
    echo 
	'<h2>Spice Round</h2>
	<p></p>The storm is in sector '.$game['storm']['location'].'</p>
	<p>A Nexus has occoured. Form alliances.</p>
	<p>The nexus will not end until everyone selects DONE.</p>
    There were sandworms in: <br>';
	
	foreach ($game['round']['sandworms'] as $x) {
		print $info['spiceDeck'][$x]['name'].'<br>';
	}
    
    echo
    '<br><form action="" method="post">
    <button name="spiceAction" value="done">Done with Nexus</button>
    </form>';
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (isset($_POST['spiceAction'])) {
    if ($_POST['spiceAction'] == 'done') {
        dune_readData();
        $game['meta']['next'][$_SESSION['faction']] = 'wait';
        dune_writeData($_SESSION['faction'].' is done with the Nexus.');
    }
    refreshPage();
}
//######################################################################
//###### Actions #######################################################
//######################################################################

function spiceAction_spiceBlow() {
    global $game, $info;
    
    // Double spice blow.
    for ($i = 1; $i <= 2; $i += 1) {
		$cardTemp = $game['spiceDeck']['deck-'.$i][0];
		if ($info['spiceDeck'][$cardTemp]['type'] == 'worm') {
			$game['round']['nexus'] = true;
	        foreach (array('[A]','[E]','[F]','[G]','[H]') as $faction) {
		        $game['meta']['next'][$faction] = 'nexus';
		    }
		    $game['round']['sandworms'][] = $game['spiceDeck']['discard-'.$i];
		    dune_dealSpice($i);
		    while ($info['spiceDeck'][$game['spiceDeck']['deck-'.$i][0]]['type'] == 'worm') {
				$game['round']['freeSandworms'] += 1;
				dune_dealSpice($i);	
			}
		}
		$game['round']['spice-'.$i]['location'] 
					= $game['spiceDeck']['deck-'.$i][0];
		$game['round']['spice-'.$i]['spice'] 
					= $info['spiceDeck'][$game['spiceDeck']['deck-'.$i][0]]['spice'];
	}
}	

function spiceAction_endRound() {
	global $game, $info;
	dune_readData();
	
	//## Place Spice ##############################################
	for ($i = 1; $i <= 2; $i += 1) {
		dune_gmMoveTokens('[SPICE]', $game['round']['spice-'.$i]['spice'],0,
					'[BANK]', $game['round']['spice-'.$i]['location']);
	}
	$message = 'Spice blooms in '.$game['round']['spice-1']['location'];
	$message .= ' and '.$game['round']['spice-2']['location'];
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
		$game[$faction]['alert'][] = $message;
	}	
	$game['meta']['round'] = 'bidding-round.php';
    unset($GLOBALS['game']['round']);
    dune_writeData('Spice Round ends. The Bidding Round begins.', true);
    refreshPage();
}
?>
