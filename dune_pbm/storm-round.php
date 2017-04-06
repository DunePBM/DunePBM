<?php 
// Storm Round
// Called from index.php
// setup-treachery.php --> storm-round.php --> spice-round.php
// colleciton-round.php --> storm-round.php --> spice-round.php

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
	echo 
	'<h3>Storm Round:</h3>
    <p>The storm is in Sector '.$game['storm']['location'].'.</p>
    <p>The storm will move '.($game['storm']['next'].' sectors.</p><br>';
 
    if ($game['meta']['turn'] >= 2) {
        echo
        '<select name="storm_action">
        <option value="none">Do nothing.</option>			
        <option value="wc">Play Weather Control</option>			
        </select> 
        <input type="submit" value="Go">
        </form>';
        //////////////////////////////////////////////
        echo
        '<form action="#" method="post">
        <h3>Move Tokens</h3>
        <p>Move 
        <input id="move_token_number" name="move_token_number" type="number" min=-100 max=100 value="0"/> /
        <input id="move_number_star"  name="move_number_star" type="number" min=-100 max=100 value="0"/> * tokens';
        echo dune_getTerritory('from location: ', 'token_loc_start', false, true);
        echo dune_getTerritory('to location location: ', 'token_loc_end', true, true);
        echo '</p></form>';
        '<form action="#" method="post">
            <input type="checkbox" name="wc" value="true">Play Weather Control<br>
            <input type="checkbox" name="fa" value="true">Play Family Atomics<br>
            <input type="submit" value="Submit">
        </form>';
    }
}

// Action ########################################################
if (isset($_POST)){
    if (isset($_POST['wc'])) {
        actionFunction_wc();
    }
    if (isset($_POST['wc'])) {
        actionFunction_wc();
    }
    refreshPage();
}

function actionFunction() {
    global $game, $info;
    dune_readData();
    
    dune_writeData();
    return;
}
?>
