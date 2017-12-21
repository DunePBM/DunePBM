<?php 
// Bidding Round
// Called from index.php
// storm-round.php --> bidding-round.php --> movement-round.php

// This version uses live bidding, no auto bidding.

global $game, $info;	

//######################################################################
//## First Run #########################################################
//######################################################################
if (!isset($game['round'])) {
    $game['round'] = array();
    $game['round']['history'] = array();
    $game['round']['numberOfCards'] = 0;
    $game['round']['currentCard'] = 1;
    
    biddingAction_setupBidding();
	dune_writeData('Setup Bidding Round', true);
	refreshPage();
}

//######################################################################
//###### Every Run #####################################################
//######################################################################

//## Checks for end of bidding. ########################################
$stillBidding = 0;
foreach (array('[A]', '[B]', '[E]','[F]','[G]','[H]') as $faction) {
	if ($game['meta']['next'][$faction] == 'bidding') {
		$stillBidding += 1;
	}
}
if ($stillBidding == 1) {
	biddingAction_giveCard();
	refreshPage();
}

//## Checks for end of round. ##########################################
if ($stillBidding == 0) {
	biddingAction_endRound('Everyone has passed. ');
	
}
if ($game['round']['currentCard'] > $game['round']['numberOfCards']) {
	biddingAction_endRound('All cards have been bid. ');
}

//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)) {
    global $game, $info;

	if ($game['round']['numberOfCards'] > 0) {
		echo '<h2>Bidding Round</h2>';
		
		echo '<h3>Card '.$game['round']['currentCard'].' of '
		.$game['round']['totalCards'].'</h3>';
		
		// Shows card to Atredies.
		if ($_SESSION['faction'] == '[A]') {
			echo '<p>Card up for bid: '.$game['treachery']['deck'][0].'.';
		}
		
		// Show current bid.
		echo '<p>Current high bid: '.$game['round']['highBid'].
			' by '.$game['round']['highBidder'].'.</p>';
		
		// Get bids or pass
		if ($game['meta']['next'][$_SESSION['faction']] == 'bidding') {
			echo '
			<h3>Make your bid:</h3>
			<form action="#" method="post">
			<p>Bid 
				<input id="basicBid" name="basicBid" type="number" min='.
					($game['round']['highBid']+1).
					'max=100 value="'.($game['round']['highBid']+1).'"/>
					<input type="submit" value="Submit">
			</p></form>';
		}
		echo
		'<form action="" method="post">
		<button name="passBidding" value="passBidding">Pass on Bidding</button>
		</form>';
		
		echo
		'<form action="" method="post">
		<button name="closeBidding" value="closeBidding">Close Bidding</button>
		</form>';
	}
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (!empty($_POST)) {
	global $game, $info;
	
	if (isset($_POST['basicBid'])) {
		biddingAction_basicBid($_SESSION['faction'], $_POST['basicBid']);
	}

	if (isset($_POST['passBidding'])) {
		dune_readData();
		$game['round']['next'][$_SESSION['faction']] = 'pass';
		dune_writeData('Player passes.');
	}	

	if (isset($_POST['closeBidding'])) {
		biddingAction_giveCard();
	}	
    
    refreshPage();
}

//######################################################################
//###### Actions #######################################################
//######################################################################
function biddingAction_basicBid($faction, $amount) {
	global $game, $info;
	dune_readData();
	if ($amount > $game['round']['highBid']) {
		if ($game[$faction]['spice'] >= $amount) {
			//### Gives old bid back. ###
			$game[$game['round']['highBidder']]['spice'] +=
							$game['round']['highBid'];
			//### Collects new bid. ###
			$game[$faction]['spice'] -= $amount;								
			$game['round']['highBid'] = $amount;
			$game['round']['highBidder'] = $faction;
			$game['round']['next'][$faction] = 'bidding-round.php';
			$message = $info['factions'][$faction]['name'].' bids '.$amount;
			$game['round']['history'][] = $message;
			dune_writeData($message);
		}
	}
}

function biddingAction_giveCard() {
	global $game, $info;
	dune_readData();
	$winner = $game['round']['highBidder'];
	dune_dealTreachery($winner);
	if ($winner == '[H]') {
		dune_dealTreachery($winner);
	}
	if ($winner != '[E]') {
		$game['[E]']['spice'] += $game['round']['highBid'];
	}
	$game['round']['currentCard'] += 1;
	
	$message = $info['factions'][$winner]['name'].' winns the card.';
	
	biddingAction_setupBidding();
	
	dune_writeData($message, true);
	dune_postForum($message. true);
}

function biddingAction_setupBidding() {
	global $game, $info;
	
	$game['round']['highBid'] = 0;
	$game['round']['highBidder'] = '';
	
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]') as $faction) {
		if (count($game[$faction]['treachery']) < 4) {
			$game['round']['numberOfCards'] += 1;
			$game['meta']['next'][$faction] = 'bidding';
		} else {
			$game['meta']['next'][$faction] = 'full';
		}
	}
	if (count($game['[H]']['treachery']) < 8) {
		$game['round']['numberOfCards'] += 1;
		$game['meta']['next']['[H]'] = 'bidding';
	} else {
		$game['meta']['next'][$faction] = 'full';
	}
}

function biddingAction_endRound($message = '') {
	/*global $game, $info;
	dune_readData();
	$game['meta']['round'] = 'movement-round.php';
    unset($GLOBALS['game']['round']);
    dune_writeData($message.'Bidding Round ends. The Movement Round begins.', true);
    refreshPage();*/
    gameAlert('Hit the end of bidding round.');
}
?>
