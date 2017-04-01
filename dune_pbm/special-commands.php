<?php 
// Special Commands
// Called from index.php
// uses $_SESSION['override']

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
	echo 
	'<h3>'.$info['factions'][$_SESSION['faction']]['name'].' Special Commands:</h3><br>';
    
    echo
    '<form action="" method="post">
    <h3>Undo Last Move</h3>
    <button name="special_action" value="undoMove">Undo Last Move</button>
    </form>';
    
    echo
    '<form action="#" method="post">
    <h3>Move Tokens</h3>
    <p>Move 
        <input id="move_token_number" name="move_token_number" type="number" min=-100 max=100 value="0"/> /
        <input id="move_number_star"  name="move_number_star" type="number" min=-100 max=100 value="0"/> * tokens';
        echo dune_getTerritory('from location: ', 'token_loc_start', false, true);
        echo dune_getTerritory('to location location: ', 'token_loc_end', true, true);
        echo '</p></form>';
        
    echo
    '<form action="#" method="post">
    <h3>Place/Remvoe Spice</h3>
    <p>Place 
        <input id="place_spice_number" name="place_spice_number" type="number" min=-100 max=100 value="0"/>';
        echo dune_getTerritory('spice on location: ', 'token_loc', true);
        echo '</p></form>';            
}

// Action ########################################################
if (!empty($_POST)){
    if (isset($_POST['move_token_number'])) {
        echo actionFunction_moveTokens();
        refreshPage();
    }
    if (isset($_POST['place_spice_number'])) {
        echo actionFunction_placeSpice();
        refreshPage();
    }
    if (isset($_POST['special_action'])) {
        if ($_POST['special_action'] == 'undoMove'){
            dune_undoMove();
            refreshPage();
        }
    }
}

function actionFunction_moveTokens() {
    global $game, $info;
    dune_readData();
    dune_gmMoveTokens($_SESSION['faction'], $_POST['move_token_number'], 
            $_POST['move_number_star'], $_POST['token_loc_start'], 
            $_POST['token_loc_end']);
    $game['meta']['event'] = 'Special Command: move/remove tokens';
    $game['meta']['faction'] = $_SESSION['faction'];
    dune_writeData();
    return; //'<script>alert("Action successful.");</script>';
}

function actionFunction_placeSpice() {
    global $game, $info;
    dune_readData();
    dune_gmMoveTokens('[SPICE]', $_POST['place_spice_number'], 
            0, '[BANK]', $_POST['token_loc']);
    $game['meta']['event'] = 'Special Command: Place/remove spice';
    $game['meta']['faction'] = $_SESSION['faction'];
    dune_writeData();
    return; //'<script>alert("Action successful.");</script>';
}
?>
