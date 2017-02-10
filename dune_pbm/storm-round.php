<?php 
// Storm Round
// Called from index.php
// setup-treachery.php --> storm-round.php --> spice.php
// colleciton-round.php --> storm-round.php --> spice.php

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
	echo 
	'<h3>'.$info['factions'][$_SESSION['faction']]['name'].' Storm Round:</h3>
    <p>The storm is in Sector '.$game['storm']['location'].'.</p>
    <p>The storm will move '.($game['storm']['next'].' sectors.</p><br>';
    if ($game['meta']['turn'] >= 2) {
        echo
        '<form action="#" method="post">
            <input type="checkbox" name="wc" value="true">Play Weather Control<br>
            <input type="checkbox" name="fa" value="true">Play Family Atomics<br>
            <input type="submit" value="Submit">
        </form>;
}

// Action ########################################################
if (!empty($_POST)){
    if (isset($_POST['wc']) && 
                    isset($_POST['winningTurn']) &&
                    $_SESSION['faction'] == '[B]') {
        echo actionFunction();
        echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">'; 
    }
}

function actionFunction() {
    global $game, $info;
    dune_readData();
    // Checks input.
    
    // Carry Out Action.
    $game['[B]']['prediction']['winningFaction'] = $_POST['winningFaction'];
    $game['[B]']['prediction']['winningTurn'] = $_POST['winningTurn'];        
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
