<?php 
// Movement Round
// Called from index.php
// bidding-round.php --> movement-round.php --> combat-round.php

//######################################################################
//## First Run #########################################################
//######################################################################
if (!isset($game['round'])) {
    $game['round'] = array();
        
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $i) {
		$game['meta']['next'][$i] = 'notDone';
	}
	$game['meta']['next'][$game['meta']['playerOrder'][0]] = 'placeOrders';
	$game['meta']['next']['[G]'] = 'placeOrders';
	}
	dune_writeData('Setup Movement Round', true);
	refreshPage();
}

//######################################################################
//###### Every Run #####################################################
//######################################################################


//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)){
    global $game, $info;

	echo 
	'<h2>Movement Round</h2>';
        
    if ($game['meta']['next'][$_SESSION['faction']] == 'placeOrders') {
        //Revival
        echo
        '<form action="#" method="post">
        <h3>Revival</h3>
        <p>Move 
        <input id="revivalTokenNumber" name="revivalTokenNumber" type="number" min=0 max=100 value="0"/> /
        <input id="revaivalNumber_star"  name="revival_number_star" type="number" min=-100 max=100 value="0"/> * tokens
        </p></form>';
    
        //Shipping
        echo
        '<form action="#" method="post">
        <h3>Shipping</h3>
        <p>Move 
        <input id="move_token_number" name="move_token_number" type="number" min=-100 max=100 value="0"/> /
        <input id="move_number_star"  name="move_number_star" type="number" min=-100 max=100 value="0"/> * tokens';
        echo dune_getTerritory('to location: ', 'shipping_loc', false, false);
        echo '</p></form>';
    
        //Movement
        echo
        '<form action="#" method="post">
        <h3>Movement</h3>
        <p>Move 
        <input id="move_token_number" name="move_token_number" type="number" min=-100 max=100 value="0"/> /
        <input id="move_number_star"  name="move_number_star" type="number" min=-100 max=100 value="0"/> * tokens';
        echo dune_getTerritory('from location: ', 'token_loc_start', false, false);
        echo dune_getTerritory('to location location: ', 'token_loc_end', true, false);
        echo '</p></form>';
    
    echo
    '<br><form action="" method="post">
    <button name="storm_action" value="done">Done with Storm</button>
    </form>';
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (isset($_POST['movement_action'])) {
    if ($_POST['movement_action'] == 'done') {
        dune_readData();
        $game['meta']['next'][$_SESSION['faction']] = 'wait.php';
        dune_writeData('Done with movement.');
    }
    
    //##############################################################
    //## Checks for End of Round ###################################
    //##############################################################
    dune_readData();
    dune_checkRoundEnd('movementRound', 'battle-round.php',
                                        'Movement round has ended.');
    refreshPage();
    }        
}

//######################################################################
//###### Actions #######################################################
//######################################################################
function actionFunctionRevival($x, $xStar) {
    dune_gmMoveTokens($_SESSION['faction'], $x, $xStar, '[TANKS]', '[OFF]');
}

function actionFunctionShippingl($x, $xStar, $loc) {
    dune_gmMoveTokens($_SESSION['faction'], $x, $xStar, '[OFF]', $loc);
}

function actionFunctionMovement($x, $xStar, $fromLoc, $toLoc) {
    dune_gmMoveTokens($_SESSION['faction'], $x, $xStar, $fromLoc, $toLoc);
}

?>
