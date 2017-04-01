<?php 
// The bidding round.
// Called by index.php.
// spice-round.php --> bidding-round.php --> movement-round.php

// Forms ###########################################################
if (empty($_POST)){
    echo 
	'Bidding Round:</h3>';    
    for
    <textarea rows="10" cols="20" id="text">
    
    </textarea>
		
        
        Predict winning turn: <input name="winningTurn" type="number" min=1 max=10 value="1"/>
			<input type="submit" value="Submit">
        
        <form action="#" method="post">
			Predict winning faction: <select name="winningFaction">
                <option value="[A]">Atredies</option>
                <option value="[E]">Emperor</option>
                <option value="[F]">Fremen</option>
                <option value="[G]">Guild</option>
                <option value="[H]">Harkonnen</option>
            </select><br>
            Predict winning turn: <input name="winningTurn" type="number" min=1 max=10 value="1"/>
			<input type="submit" value="Submit">
		</form>';
	}
}

// Action ########################################################
if (!empty($_POST)){
    global $debug;
    if (isset($_POST['winningFaction']) && 
                    isset($_POST['winningTurn']) &&
                    $_SESSION['faction'] == '[B]') {
        echo actionFunction();
        if (!$debug) {
            echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
        }
    }
}

function actionFunction() {
    global $game, $info;
    dune_readData();
    // Checks input.
    if (!in_array($_POST['winningFaction'], 
                    array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]'))) {
        return '<script>alert("Action failed: Not a valid faction.");</script>';
    }
    if (!is_int((int) $_POST['winningTurn'])) {
        return '<script>alert("Action failed: Winning turn is not a number.");</script>';
    }
    // Carry Out Action.
    $game['[B]']['prediction']['winningFaction'] = $_POST['winningFaction'];
    $game['[B]']['prediction']['winningTurn'] = (int) $_POST['winningTurn'];        
    $game['[B]']['notes'] =[$info['factions'][$_POST['winningFaction']]['name'].
                ' predicted to win on turn '.$_POST['winningTurn'].'.'];
    $game['meta']['event'] = 'Bene Gesserit made their prediction.';
    $game['meta']['faction'] = '[B]';
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]') as $faction) {
        for ($i = 0; $i <4; $i++) {
            $game['meta']['next'][$faction] = 'choose-traitors.php';
        }
    }
    $game['meta']['next']['[H]'] = 'wait.php';
    dune_writeData();
    return '<script>alert("Action successful.");</script>';
}
?>
