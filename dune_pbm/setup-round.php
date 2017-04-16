<?php 
// The setup round.
// Called by index.php.
// setup-round.php --> storm-round.php

//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)){
    global $game, $info;
    
    //##############################################################
    //## First Run #################################################
    //##############################################################
    if (!isset($game['setupRound'])) {
        $game['setupRound'] = array();
        foreach (array('[A]','[E]','[F]','[G]','[H]') as $x) {
            $game['setupRound']['next'][$x] = 'wait';
        }
        $game['setupRound']['next']['[B]'] = 'makePrediction';
    }

    //##############################################################
    //## Make Prediction ###########################################
    //##############################################################
    if ($game['setupRound'['next'][$_SESSION['faction']] == 'makePrediction') {
        if ($_SESSION['faction'] == '[B]') {
            echo 
            '<h3>Bene Gesserit:</h3>
            <form action="#" method="post">
                Predict winning faction: <select name="winningFaction">
                    <option value="[A]">Atreides</option>
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
    
    //##############################################################
    //## Choose Traitors ###########################################
    //##############################################################
    if ($game['setupRound'['next'][$_SESSION['faction']] == 'chooseTraitors') {
		echo 
		'<h3>'.$info['factions'][$_SESSION['faction']]['name'].':</h3>
        
		<form action="#" method="post">
			Choose your traitor: 
            <select name="traitor">
            <option value="0">'.
            $info['leaders'][$game['traitorDeck'][$_SESSION['faction']][0]]['name'].
            ' '.$info['leaders'][$game['traitorDeck'][$_SESSION['faction']][0]]['faction'].
            '</option>
            <option value="1">'.
            $info['leaders'][$game['traitorDeck'][$_SESSION['faction']][1]]['name'].
            ' '.$info['leaders'][$game['traitorDeck'][$_SESSION['faction']][1]]['faction'].
            '</option>
            <option value="2">'.
            $info['leaders'][$game['traitorDeck'][$_SESSION['faction']][2]]['name'].
            ' '.$info['leaders'][$game['traitorDeck'][$_SESSION['faction']][2]]['faction'].
            '</option>
            <option value="3">'.
            $info['leaders'][$game['traitorDeck'][$_SESSION['faction']][3]]['name'].
            ' '.$info['leaders'][$game['traitorDeck'][$_SESSION['faction']][3]]['faction'].
            '</option>
            </select>
			<input type="submit" value="Submit">
		</form>';
    }
}

//######################################################################
//###### Forms #########################################################
//######################################################################
if ((!empty($_POST)) && (isset($game['setupRound']))) {
    global $game, $info;
    
    //##############################################################
    //## Make Prediction ###########################################
    //##############################################################
    if ($game['setupRound']['next'][$_SESSION['faction'] == 'makePrediction') {
        if (isset($_POST['winningFaction']) && 
                    isset($_POST['winningTurn']) &&
                    $_SESSION['faction'] == '[B]') {
            actionMakePrediction();
    }
    
    //##############################################################
    //## Choose Traitors ###########################################
    //##############################################################
    if ($game['setupRound']['next'][$_SESSION['faction'] == 'chooseTraitors') {
        if (isset($_POST['traitor'])) {
            actionChooseTraitors();
        }
    }
    
    //##############################################################
    //## Wait ######################################################
    //##############################################################
    if ($game['setupRound']['next'][$_SESSION['faction'] == 'wait') {
        dune_getWaiting();
    }
    
    refreshPage();
}

//######################################################################
//###### Actions #######################################################
//######################################################################

function actionMakePrediction() {
    global $game, $info;
    dune_readData();
    // Checks input.
    if (!in_array($_POST['winningFaction'], 
                    array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]'))) {
        return;
    }
    if (!is_int((int) $_POST['winningTurn'])) {
        return;
    }
    // Carry Out Action.
    $game['[B]']['prediction']['winningFaction'] = $_POST['winningFaction'];
    $game['[B]']['prediction']['winningTurn'] = (int) $_POST['winningTurn'];        
    $game['[B]']['notes'] =[$info['factions'][$_POST['winningFaction']]['name'].
                ' predicted to win on turn '.$_POST['winningTurn'].'.'];
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]') as $faction) {
        for ($i = 0; $i <4; $i++) {
            $game['setupRound']['next'][$faction] = 'chooseTraitors';
        }
    }
    $game['setupRound']['next']['[H]'] = 'wait';
    dune_writeData('Bene Gesserit made their prediction.');
    return;
}

function actionChooseTraitors() {
    global $game, $info;
    dune_readData();
    // Checks input.
    
    // Carry Out Action.
    $game[$_SESSION['faction']]['traitors'] 
            = array($game['traitorDeck'][$_SESSION['faction']][(int)$_POST['traitor']]);
    $game['setupRound']['next'][$_SESSION['faction']] = 'wait';
    $isDone = true;
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
        if ($game['setupRound']['next'][$faction] != 'wait') {
            $isDone = false;
        }
    }
    if ($isDone) {
        $game['setupRound']['next']['[F]'] = 'setupTokens';
        foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
            $temp = 'Initial traitors: ';
            foreach ($game['traitorDeck'][$faction] as $x) {
                $temp .= $info['leaders'][$x]['name'].', ';
            }
            $temp = substr($temp, 0, -2);
            $game[$faction]['notes'][] = $temp;
        }
        
        unset($game['traitorDeck']);
    }
    $message = $info['factions'][$_SESSION['faction']]['name'].' chose their traitor.'
    dune_writeData($message); 
    return;
}

?>

 
 
    
}




?>
