<?php 
// Special Commands
// Called from index.php
// uses $_SESSION['override']

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
	echo 
	'<h2>Special Commands</h2>';
    
    echo
    '<form action="#" method="post">
    <h3>Move Tokens</h3>
    <p>move 
        <input id="move_token_number" name="move_token_number" type="number" min=-100 max=100 value="0"/> /
        <input id="move_number_star"  name="move_number_star" type="number" min=-100 max=100 value="0"/> *
        tokens for  
        <select name="faction">
			<option value="[A]">Atredies</option>
			<option value="[B]">Bene Gesserit</option>
			<option value="[E]">Emperor</option>
			<option value="[F]">Fremen</option>
			<option value="[G]">Guild</option>
			<option value="[H]">Harkonnen</option>
		</select>';
        echo dune_getTerritory('from location: ', 'token_loc_start', false, true);
        echo dune_getTerritory('to location location: ', 'token_loc_end', true, true);
        echo '</p></form>';
        
    echo
    '<form action="#" method="post">
    <h3>Place/Remvoe Spice</h3>
    <p>Place 
        <input id="place_spice_number" name="place_spice_number" type="number" min=-100 max=100 value="0"/>';
        echo dune_getTerritory('on location: ', 'token_loc', true);
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
}

function actionFunction_moveTokens() {
    global $game, $info;
    dune_readData();
    dune_gmMoveTokens($_POST['faction'], $_POST['move_token_number'], 
            $_POST['move_number_star'], $_POST['token_loc_start'], 
            $_POST['token_loc_end']);
    $game['meta']['event'] = 'Special Command: move/remove tokens';
    $game['meta']['faction'] = $_SESSION['faction'];
    dune_writeData();
    return '<script>alert("Action successful.");</script>';
}

function actionFunction_placeSpice() {
    global $game, $info;
    dune_readData();
    dune_gmMoveTokens('[SPICE]', $_POST['place_spice_number'], 
            0, '[BANK]', $_POST['token_loc']);
    $game['meta']['event'] = 'Special Command: Place/remove spice';
    $game['meta']['faction'] = $_SESSION['faction'];
    dune_writeData();
    return '<script>alert("Action successful.");</script>';
}
?>
