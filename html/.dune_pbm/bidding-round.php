<?php 
// Bidding Round
// Called from index.php
// storm-round.php --> bidding-round.php --> movement-round.php

//######################################################################
//## First Run #########################################################
//######################################################################
if (!isset($game['biddingRound'])) {
	global $game, $info;	
    $game['biddingRound'] = array();
    $game['biddingRound']['biddingOrder'] = array_cycle($game['meta']['playerOrder'], false);
    // above: Because biddingAction_setupBidding() cycles the order,
    // so we uncycle it once.
    $game['biddingRound']['numberOfCards'] = 0;
    $game['biddingRound']['currentCard'] = 1;
    $game['biddingRound']['totalCards'] = 0;
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]') as $faction) {
		if (count($game[$faction]['treachery']) < 4) {
			$game['biddingRound']['numberOfCards'] += 1;
		}
	}
	if (count($game['[H]']['treachery']) >= 8) {
		$game['biddingRound']['numberOfCards'] += 1;
	}
	$game['biddingRound']['totalCards'] = $game['biddingRound']['numberOfCards'];
	biddingAction_setupBidding(false);
	dune_writeData('Setup Bidding Round', true);
	refreshPage();
}

//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)) {
    global $game, $info;

	if ($game['biddingRound']['numberOfCards'] > 0) {
		echo '<h2>Bidding Round</h2>';
		
		echo '<h3>Card '.$game['biddingRound']['currentCard'].' of '
		.$game['biddingRound']['totalCards'].'</h3>';
		
		// Shows card to Atredies.
		if ($_SESSION['faction'] == '[A]') {
			echo '<p>Card up for bid: '.$game['treachery']['deck'][0].'.';
		}
		
		// Show players still bidding.
		echo '<p>Players still bidding: <br>';
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			if ($game['biddingRound']['next'][$faction] == 'bidding-round.php') {
				echo $faction.' <br>';
			}
		}
		
		// Show current bid.
		echo '<p>Current high bid: '.$game['biddingRound']['highBid'].
			' by '.$game['biddingRound']['highBidder'].'.</p>';
		
		// Get bids or pass
		echo
		'<h3>Make your bid:</h3>';
		
		$tempArray = array_diff(
						array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]'), 
						$_SESSION['faction']);
		foreach ($tempArray as $faction) {
			echo
            '<p>Bid vs '.$faction.':</p>';
            echo
            '<form action="#" method="post">
                Increase bid by <input id="howMuchVs'.$faction.'" 
                name="howMuchVs'.$faction.'" type="number" min=0 max=100 value="1"/> <br>
                Bid up to <input id="howHighVs'.$faction.'" 
                name="howHighVs'.$faction.'" type="number" min=0 max=100 value="0"/> <br>
            </form>';
        }
        
        echo
        '<input type="submit" value="Submit">';
		
		echo
		'<form action="#" method="post">
		<p>Bid 
			<input id="manualBid" name="manualBid" type="number" min='.
				($game['biddingRound']['highBid']+1).
				'max=100 value="'.($game['biddingRound']['highBid']+1).'"/>
				<input type="submit" value="Submit">
		</p></form>';
		
		echo
		'<form action="" method="post">
		<button name="passBidding" value="passBidding">Pass on Bidding</button>
		</form>';
		
		echo
		'<form action="" method="post">
		<button name="closeBidding" value="closeBidding">Close Bidding</button>
		</form>';
	}
	
	if ($game['biddingRound']['numberOfCards'] <= 0) {
	
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
		$game['biddingRound']['next'][$_SESSION['faction']] = 'wait.php';
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
	if ($amount > $game['biddingRound']['highBid']) {
		if ($game[$faction]['spice'] >= $amount) {
			//### Gives old bid back. ###
			$game[$game['biddingRound']['highBidder']]['spice'] +=
							$game['biddingRound']['highBid'];
			//### Collects new bid. ###
			$game[$faction]['spice'] -= $amount;								
			$game['biddingRound']['highBid'] = $amount;
			$game['biddingRound']['highBidder'] = $faction;
			$game['biddingRound']['next'][$faction] = 'bidding-round.php';
		}
	}
}

function biddingAction_autoBid() {
	global $game, $info;
	dune_readData();
	while (true) {
		$startFaction = $game['biddingRound']['highBidder'];
		$startBid = $game['biddingRound']['highBid'];
		$vsFaction = $game['biddingRound']['highBidder'];
		$vsBid = $game['biddingRound']['highBid'];
		foreach ($game['biddingRound']['biddingOrder'] as $faction) {
			$vsFaction = $game['biddingRound']['highBidder'];
			$vsBid = $game['biddingRound']['highBid'];
			if ($game['biddingRound']['howHighToBid']
									[$faction][$vsFaction] > $vsBid) {
				$newBid = $vsBid  + $game['biddingRound']['howMuchToBid']
													[$faction][$vsFaction];
				biddingAction_manualBid($faction, $newBid);
			}
		}
		if ($startFaction == $vsFaction) {
			if ($startBid == $vsBid) {
				return;
			}
		}
	}
	dune_writeData("Bidding.");
}

function biddingAction_giveCard() {
	global $game, $info;
	dune_readData();
	$winner = $game['biddingRound']['highBidder'];
	dune_dealTreachery($winner);
	if ($winner == '[H]') {
		dune_dealTreachery($winner);
	}
	if ($winner != '[E]') {
		$game['[E]']['spice'] += $game['biddingRound']['highBid'];
	}
	$game['biddingRound']['numberOfCards'] -= 1;
	
	$message = $game['biddingRound']['winningBidder'].' winns the card.';
	
	// Resets bidding
	if ($game['biddingRound']['numberOfCards'] > 0) {
		biddingAction_setupBidding();
	}
	if ($game['biddingRound']['numberOfCards'] <= 0) {
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			$game['biddingRound']['next'][$faction] = 'wait.php';
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
	$game['biddingRound']['highBid'] = 0;
    $game['biddingRound']['highBidder'] = '';
    $game['biddingRound']['ableToBid'] = array();
    $game['biddingRound']['howMuchToBid'] = array();
    $game['biddingRound']['howHighToBid'] = array();
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
		$game['biddingRound']['next'][$faction] = 'bidding-round.php';
		$game['biddingRound']['ableToBid'][$faction] = true;
		$game['biddingRound']['howMuchToBid'][$faction] = array();
		$game['biddingRound']['autoBidSet'][$faction] = false;
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction2) {
			$game['biddingRound']['howMuchToBid'][$faction][$faction2] = 0;
			$game['biddingRound']['howHighToBid'][$faction][$faction2] = 0;
		}
	}
	$game['biddingRound']['biddingOrder'] 
				= array_cycle($game['biddingRound']['biddingOrder']);
	
	// Gets only valid bidders.
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
		$game['biddingRound']['next'][$faction] = 'bidding-round.php';
	}
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]') as $faction) {
		if (count($game[$faction]['treachery']) >= 4) {
			$game['biddingRound']['next'][$faction] = 'wait.php';
			$changesMade = true;
		}
	}
	if (count($game['[H]']['treachery']) >= 8) {
		$game['biddingRound']['next']['[H]'] = 'wait.php';
		$changesMade = true;
	}
	if ($game['biddingRound']['numberOfCards'] <= 0) {
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			$game['biddingRound']['next'][$faction] = 'wait.php';
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
		if ($game['biddingRound']['next'][$faction] == 'bidding-round.php') {
			$i += 1;
		}
	}
	if ($i == 1) {
		biddingAction_giveCard();
	}
	if ($i == 0) {
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			$game['biddingRound']['next'][$faction] = 'wait.php';
		}
	}
	//dune_checkRoundEnd('biddingRound', 'movement-round.php',
    //                                    'Bidding round has ended.', true);
	refreshPage();
}
?>
