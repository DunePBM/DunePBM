<?php 
// Bidding Round
// Called from index.php
// storm-round.php --> bidding-round.php --> movement-round.php

//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)){
    global $game, $info;

    //##############################################################
    //## First Run #################################################
    //##############################################################
    if (!isset($game['biddingRound'])) {
        $game['biddingRound'] = array();
        $game['biddingRound']['numberOfCards'] = 6; //I know this is wrong;
        $game['biddingRound']['biddingOrder'] = $game['meta']['playerOrder'];
        $game['biddingRound']['highBid'] = 0;
        $game['biddingRound']['highBidder'] = '';
    }
    
    //##############################################################
    //## Every Run #################################################
    //##############################################################
	echo '<h2>Bidding Round</h2>';
	
	// Shows card to Atredies
	if ($_SESSION('faction') == '[A]') {
		echo '<p>Card up for bid: '.$game['treachery']['deck'][0].'.';
	}
	
	echo '<p>Current high bid: '.$game['biddingRound']['highBid'].
		' by '.$game['biddingRound']['highBidder'].'.</p>';
	
	echo
    '<form action="#" method="post">
    <p>Bid 
        <input id="bid" name="bid" type="number" min='.
			($game['biddingRound']['highBid']+1).'max=100 value="0"/>
			<input type="submit" value="Submit">
    </p></form>';
    
    echo
    '<form action="" method="post">
    <button name="closeBidding" value="closeBidding">Close Bidding</button>
    </form>';
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (isset($_POST['bid'])) {
    if ($_POST['bid'] > $game['biddingRound']['highBid']) {
        $game['biddingRound']['highBid'] = $_POST['bid'];
        $game['biddingRound']['highBider'] = $_SESSION['faction'];
    }
        
    //##############################################################
    //## Checks for End of Round ###################################
    //##############################################################
    dune_readData();
    dune_checkRoundEnd('biddingRound', 'movement-round.php',
                                        'Bidding round has ended.');
    refreshPage();
}
?>
