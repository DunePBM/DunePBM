<?php 
// Bidding Round
// Called from index.php
// storm-round.php --> bidding-round.php --> movement-round.php

// This version uses live bidding, with bidding order to settle ties.

global $game, $info;	

//######################################################################
//## First Run #########################################################
//######################################################################
if (!isset($game['round'])) {
    $game['round'] = array();
    $game['round']['biddingOrder'] = array_cycle($game['meta']['playerOrder'], false);
    // above: Because biddingAction_setupBidding() cycles the order,
    // so we uncycle it once.
    $game['round']['numberOfCards'] = 0;
    $game['round']['currentCard'] = 1;
    $game['round']['totalCards'] = 0;
    $game['round']['biddingDone'] = array();
    $game['round']['bidVsNoBid'] = array();
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]') as $faction) {
		if (count($game[$faction]['treachery']) < 4) {
			$game['round']['numberOfCards'] += 1;
			$game['meta']['next'][$faction] = 'biddingRound';
		}
	}
	if (count($game['[H]']['treachery']) < 8) {
		$game['round']['numberOfCards'] += 1;
		$game['meta']['next']['[H]'] = 'biddingRound';
	}
	$game['round']['totalCards'] = $game['round']['numberOfCards'];
	biddingAction_setupBidding(false);
	dune_writeData('Setup Bidding Round', true);
	refreshPage();
}

//######################################################################
//###### Every Run #####################################################
//######################################################################

//## Checks for end of bidding. ########################################
$isBiddingDone = true;
foreach (array('[A]', '[B]', '[E]','[F]','[G]','[H]') as $faction) {
	if ($game['round']['biddingDone'][$faction] = false) {
		$isBiddingDone = false;
	}
}
if ($isBiddingDone) {
	biddingAction_autoBid();
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
		
		// Show players still bidding.
		echo '<p>Players still bidding: <br>';
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			if ($game['round']['next'][$faction] == 'bidding-round.php') {
				echo $faction.' <br>';
			}
		}
		
		// Show current bid.
		echo '<p>Current high bid: '.$game['round']['highBid'].
			' by '.$game['round']['highBidder'].'.</p>';
		
		// Get bids or pass
		if ($game['meta']['next'][$_SESSION['faction']] == 'biddingRound') {
			echo
			'<h3>Make your bid:</h3>';
			foreach (array_diff(
							array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]'), 
							array($_SESSION['faction'])) as $faction) {
				echo
				'<p><form action="#" method="post">Bid vs '.
				$info['factions'][$faction]['name'].': Increase bid by <input id="howMuchVs'.$faction.'" 
					name="howMuchVs'.$faction.'" type="number" min=0 max=100 value="1"/> 
					up to <input id="howHighVs'.$faction.'" 
					name="howHighVs'.$faction.'" type="number" min=0 max=100 value="0"/> spice.<br>
				</form></p>';
			}
			echo
			'<input type="submit" value="Submit">';
		}
        
		
		/*echo
		'<form action="#" method="post">
		<p>Bid 
			<input id="manualBid" name="manualBid" type="number" min='.
				($game['round']['highBid']+1).
				'max=100 value="'.($game['round']['highBid']+1).'"/>
				<input type="submit" value="Submit">
		</p></form>';*/
		
		echo
		'<form action="" method="post">
		<button name="passBidding" value="passBidding">Pass on Bidding</button>
		</form>';
		
		echo
		'<form action="" method="post">
		<button name="closeBidding" value="closeBidding">Close Bidding</button>
		</form>';
	}
	
	if ($game['round']['numberOfCards'] <= 0) {
	
	}
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (!empty($_POST)) {
	global $game, $info;
	

	
	if (isset($_POST['bid'])) {
		dune_readData();

		dune_writeData('Player submits bidding order.');
	}

	
	if (isset($_POST['manualBid'])) {
		dune_readData();
		if (biddingAction_manualBid($_SESSION['faction'], $_POST['manualBid'])) {
			dune_writeData('Player bids.');
		}
	}

	if (isset($_POST['passBidding'])) {
		dune_readData();
		$game['round']['next'][$_SESSION['faction']] = 'wait.php';
		dune_writeData('Player passes.');
	}	

	
	if (isset($_POST['closeBidding'])) {
		biddingAction_giveCard();
	}	
    
    //##############################################################
    //## Checks for End of Round ###################################
    //##############################################################
    biddingAction_checkEnd();
    refreshPage();
}

//######################################################################
//###### Actions #######################################################
//######################################################################
function biddingAction_manualBid($faction, $amount) {
	global $game, $info;
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
		}
	}
}

function biddingAction_autoBid() {
	global $game, $info;
	dune_readData();
	while (true) {
		$startFaction = $game['round']['highBidder'];
		$startBid = $game['round']['highBid'];
		$vsFaction = $game['round']['highBidder'];
		$vsBid = $game['round']['highBid'];
		foreach ($game['round']['biddingOrder'] as $faction) {
			$vsFaction = $game['round']['highBidder'];
			$vsBid = $game['round']['highBid'];
			if ($vsFaction == '') {
				$newBid = $game['round']['openingBid'][$faction];
				biddingAction_manualBid($faction, $newBid);
				$message = $info['factions'][$faction]['name'].
							' has bid '.$newBid.' spice on card '.
							$game['round']['currentCard'].'.';
				dune_writeData($message);
			} else {
				if ($game['round']['howHighToBid'][$faction][$vsFaction] > $vsBid) {
					$newBid = $vsBid  + $game['round']['howMuchToBid']
														[$faction][$vsFaction];
					if ($newBid > $game['round']['howHighToBid'][$vsFaction]) {
						$newBid = $game['round']['howHighToBid'][$vsFaction])
					}
					biddingAction_manualBid($faction, $newBid);
					$message = $info['factions'][$faction]['name'].
								' has bid '.$newBid.' spice on card '.
								$game['round']['currentCard'].'.';
					dune_writeData($message);
				}
			}
		}
		if ($startFaction == $vsFaction) {
			if ($startBid == $vsBid) {
				break;
			}
		}
	}
	biddingAction_giveCard();
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
	$game['round']['numberOfCards'] -= 1;
	
	$message = $game['round']['winningBidder'].' winns the card.';
	
	// Resets bidding
	if ($game['round']['numberOfCards'] > 0) {
		biddingAction_setupBidding();
	}
	if ($game['round']['numberOfCards'] <= 0) {
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			$game['round']['next'][$faction] = 'wait.php';
		}
	}
	dune_writeData($message, true);
	dune_postForum($message. true);
}

function biddingAction_setupBidding($loadData = true) {
	// Sets starting values.
	global $game, $info;
	if ($loadData) {
		dune_readData();
	}
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
	}	
	
	$game['round']['highBid'] = 0;
    $game['round']['highBidder'] = '';
    $game['round']['ableToBid'] = array();
    $game['round']['howMuchToBid'] = array();
    $game['round']['howHighToBid'] = array();
    $game['round']['bidVsNoBid'] = array();
    $game['round']['openingBid'] = array();
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
		$game['round']['bidVsNoBid'][$faction] = 0;
		$game['round']['openingBid'][$faction] = 0;
	 	$game['meta']['next'][$faction] = 'wait';
	 	$game['round']['biddingDone'][$faction] = false;

		$game['round']['next'][$faction] = 'biddingRound';
	
		$game['round']['ableToBid'][$faction] = true;
		$game['round']['howMuchToBid'][$faction] = array();
	 	$game['meta']['next'][$faction] = 'biddingRound';
	 	$game['round']['biddingDone'][$faction] = false;
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction2) {
			$game['round']['howMuchToBid'][$faction][$faction2] = 0;
			$game['round']['howHighToBid'][$faction][$faction2] = 0;
		}
	}
	$game['round']['biddingOrder'] 
				= array_cycle($game['round']['biddingOrder']);
	
	// Gets only valid bidders.
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
		$game['round']['next'][$faction] = 'biddingRound';
	}
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]') as $faction) {
		if (count($game[$faction]['treachery']) >= 4) {
			$game['round']['next'][$faction] = 'wait.php';
			$changesMade = true;
		}
	}
	if (count($game['[H]']['treachery']) >= 8) {
		$game['round']['next']['[H]'] = 'wait.php';
		$changesMade = true;
	}
	if ($game['round']['numberOfCards'] <= 0) {
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			$game['round']['next'][$faction] = 'wait.php';
		}
		dune_writeData('All cards have been auctioned.', true);
		dune_postForum('All cards have been auctioned.', true);
	} else {
		dune_writeData('Next card up for bid.', true);
		dune_postForum('Next card up for bid.', true);
	}
}

function biddingAction_checkEnd() {
	// See if there is only one bidder left.
	global $game, $info;
	dune_readData();
	$i = 0;
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
		if ($game['round']['next'][$faction] == 'bidding-round.php') {
			$i += 1;
		}
	}
	if ($i == 1) {
		biddingAction_giveCard();
	}
	if ($i == 0) {
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			$game['round']['next'][$faction] = 'wait.php';
		}
	}
	//dune_checkRoundEnd('round', 'movement-round.php',
    //                                    'Bidding round has ended.', true);
	refreshPage();
}
?>
