<?php 
// Special Commands
// Called from index.php
// uses $_SESSION['override']

//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)){
    global $game, $info;
	echo 
	'<h3>'.$info['factions'][$_SESSION['faction']]['name'].
    ' Special Commands:</h3><br>';
    
    //## Undo Last Move ##########################################
    echo
    '<form action="" method="post">
    <h3>Undo Last Move</h3>
    <button name="specialAction" value="undoMove">Undo Last Move</button>
    </form>';
    
    //## Show Treachery ##########################################
    echo
    '<form action="#" method="post">
    <h3>Show Treachery</h3>
    <p><select name="showTreachery">';
    foreach ($game[$_SESSION['faction']]['treachery'] as $x) {
        echo
        '<option value="'.$x.'">'.
        $info['treachery'][$x]['name'].'</option>';
    }
    echo
    '</select>
	<input type="submit" value="Submit">
	</form></p>';
    
    //## Discrad Treachery ########################################
    echo
    '<form action="#" method="post">
    <h3>Discard Treachery</h3>
    <p><select name="discardTreachery">';
    foreach ($game[$_SESSION['faction']]['treachery'] as $x) {
        echo
        '<option value="'.$x.'">'.
            $info['treachery'][$x]['name'].'</option>';
    }
    echo
    '</select>
	<input type="submit" value="Submit">
	</form></p>';
    
    //## Move Tokens ###############################################
    echo
    '<form action="#" method="post">
    <h3>Move Tokens</h3>
    <p>Move 
        <input id="moveTokenNumber" name="moveTokenNumber" 
        type="number" min=-100 max=100 value="0"/> /
        <input id="moveNumberStar"  name="moveNumberStar" 
        type="number" min=-100 max=100 value="0"/> * tokens';
        echo dune_getTerritory('from location: ', 'tokenStartLoc', false, true);
        echo dune_getTerritory('to location location: ', 'tokenEndLoc', true, true);
        echo '</p></form>';
        
    // Add/Remove Spice
    echo
    '<form action="#" method="post">
    <h3>Place/Remvoe Spice</h3>
    <p>Place 
        <input id="place_spice_number" name="place_spice_number" type="number" min=-100 max=100 value="0"/>';
        echo dune_getTerritory('spice on location: ', 'token_loc', true);
        echo '</p></form>';            
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (!empty($_POST)){
    if (isset($_POST['moveTokenNumber'])) {
        echo actionFunction_moveTokens();
        refreshPage();
    }
    if (isset($_POST['showTreachery'])) {
        echo actionFunction_showCard();
        refreshPage();
    }
    if (isset($_POST['dicardTreachery'])) {
        echo actionFunction_discardCard();
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

//######################################################################
//###### Actions #######################################################
//######################################################################

function actionFunction_showCard() {
    global $game, $info;
    dune_readData();
    $message = $info['factions'][$_SESSION['faction']]['name'].' shows a ';
    $message .= $info['treachery'][$_POST['showTreachery']]['name'].' card.';
    dune_postForum($message, true);
    dune_writeData('Show card: '.$info['treachery'][$_POST['showTreachery']]['name'].'.');
    return;
}

function actionFunction_discardCard() {
    global $game, $info;
    dune_readData();
    dune_discardTreachery($_SESSION['faction'], $_POST['dicardTreachery']);
    dune_writeData('Discard card: '.$info['treachery'][$_POST['dicardTreachery']]['name'].'.');
    $message = $info['factions'][$_SESSION['faction']]['name'].' discards a ';
    $message .= $info['treachery'][$_POST['dicardTreachery']]['name'].' card.';
    dune_postForum($message, true);
    return;
}

function actionFunction_moveTokens() {
    global $game, $info;
    dune_readData();
    dune_gmMoveTokens($_SESSION['faction'], $_POST['moveTokenNumber'], 
            $_POST['moveNumberStar'], $_POST['tokenStartLoc'], 
            $_POST['tokenEndLoc']);
    dune_writeData('Special Command: move/remove tokens');
    return;
}

function actionFunction_placeSpice() {
    global $game, $info;
    dune_readData();
    dune_gmMoveTokens('[SPICE]', $_POST['place_spice_number'], 
            0, '[BANK]', $_POST['token_loc']);
    $game['meta']['event'] = 'Special Command: Place/remove spice';
    $game['meta']['faction'] = $_SESSION['faction'];
    dune_writeData();
    return;
}
?>
