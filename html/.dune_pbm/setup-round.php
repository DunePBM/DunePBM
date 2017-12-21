<?php 
// The Setup Round
// Called by index.php.
// setup-round.php --> storm-round.php

global $game, $info;

//######################################################################
//## First Run #########################################################
//######################################################################
if (!isset($game['round'])) {
    $game['meta']['round'] = 'setup-round.php';
    $game['round'] = array();
    foreach (array('[A]', '[B]', '[E]','[F]','[G]','[H]') as $faction) {
        $game['meta']['next'][$faction] = 'chooseTraitors';
        
        $game['round']['choseTraitors'][$faction] = false;
    }
    $game['round']['choseTraitors']['[H]'] = true;
    $game['meta']['next']['[H]'] = 'wait';
    $game['meta']['next']['[B]'] = 'makePrediction';
    dune_writeData('Start setup.', true, false);
}

//######################################################################
//## Every Round #######################################################
//######################################################################
if (isset($game['round'])) {
    //##############################################################
    //## Checks for end of round. ##################################
    //##############################################################
    $isGameDone = true;
    foreach (array('[A]', '[B]', '[E]','[F]','[G]','[H]') as $faction) {
        if ($game['meta']['next'][$faction] != 'wait') {
            $isGameDone = false;
        }
    }
    if ($isGameDone) {
        setupAction_setupTreachery();
        dune_readData();
        $game['meta']['round'] = 'storm-round.php';
        foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
            $game['meta']['next'][$faction] = 'stormRound';
        }
        unset($GLOBALS['game']['round']);
        dune_writeData('Setup Round is over. The Storm Round begins.', true);
        dune_writeForum('Setup Round is over. The Storm Round begins.', true);
        refreshPage();
    }
}
     
//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)) {
    global $game, $info;

    //##############################################################
    //## Make Prediction ###########################################
    //##############################################################
    if ($game['meta']['next'][$_SESSION['faction']] == 'makePrediction') {
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
    if ($game['meta']['next'][$_SESSION['faction']] == 'chooseTraitors') {
        echo 
        '<h3>'.$info['factions'][$_SESSION['faction']]['name'].':</h3>
        
        <p>Your traitors are:<br>';
        for ($i = 0; $i < 4; $i++) {
			echo 
			$info['leaders'][$game['traitorDeck'][$_SESSION['faction']][$i]]['name'].
            ' '.$info['leaders'][$game['traitorDeck'][$_SESSION['faction']][$i]]['faction'].'<br>';
		}
        echo '</p>
        
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
    
    //##############################################################
    //## Setup Tokens ##############################################
    //##############################################################
    if ($game['meta']['next'][$_SESSION['faction']] == 'setupTokens') {
        if ($_SESSION['faction'] == '[F]') {
            echo 
            '<h3>Fremen:</h3>
            <p>Place your 10 starting tokens.</p>
            <form action="#" method="post">
                Place <input id="st" name="st" type="number" min=0 max=10 value="0"/> /
                <input id="stStar" name="stStar" type="number" min=0 max=3 value="0"/> 
                * in Sietch Tabr.<br>
                
                Place <input id="fww" name="fww" type="number" min=0 max=10 value="0"/> /
                <input id="fwwStar"  name="fwwStar" type="number" min=0 max=3 value="0"/> 
                * on <select name="fwwSector">
                <option value="[FWW-1]">False Wall West (Sector 16)</option>
                <option value="[FWW-2]">False Wall West (Sector 17)</option>
                <option selected="selected" value="[FWW-3]">False Wall West (Sector 18)</option>
                </select><br>
                
                Place <input id="fws"  name="fws" type="number" min=0 max=10 value="0"/> /
                <input id="fwsStar"  name="fwsStar" type="number" min=0 max=3 value="0"/> 
                * on <select name="fwsSector">
                <option value="[FWS-1]">False Wall South (Sector 4)</option>
                <option selected="selected" value="[FWS-2]">False Wall South (Sector 5)</option>
                </select><br>
                
                <input type="submit" value="Submit">
            </form>';
        }
        if ($_SESSION['faction'] == '[B]') {
            echo 
            '<h3>Bene Gesserit:</h3>';
            dune_getTerritory('Select your token location: ', 'token_loc', true);
        }
    }
    
    //##############################################################
    //## Wait ######################################################
    //##############################################################
    if ($game['meta']['next'][$_SESSION['faction']] == 'wait') {
        dune_getWaiting();
    }
}

//######################################################################
//###### Forms #########################################################
//######################################################################
if ((!empty($_POST)) && (isset($game['round']))) {
    global $game, $info;
    
    //##############################################################
    //## Make Prediction ###########################################
    //##############################################################
    if ($game['meta']['next'][$_SESSION['faction']] == 'makePrediction') {
        if (isset($_POST['winningFaction']) && 
                    isset($_POST['winningTurn']) &&
                    $_SESSION['faction'] == '[B]') {
            setupAction_MakePrediction();
        }
    }
    
    //##############################################################
    //## Choose Traitors ###########################################
    //##############################################################
    if ($game['meta']['next'][$_SESSION['faction']] == 'chooseTraitors') {
        if (isset($_POST['traitor'])) {
            setupAction_chooseTraitors();
        }
    }
    
    //##############################################################
    //## Setup Tokens ##############################################
    //##############################################################
    if ($game['meta']['next'][$_SESSION['faction']] == 'setupTokens') {
        if (isset($_POST['st']) && ($_SESSION['faction'] == '[F]')) {
            setupAction_setupTokens();
        }
        if (isset($_POST['token_loc']) && ($_SESSION['faction'] == '[B]')) {
            setupAction_setupTokens();
        }
    }
    refreshPage();
}

//######################################################################
//###### Actions #######################################################
//######################################################################

function setupAction_makePrediction() {
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
    $game['meta']['next']['[B]'] = 'chooseTraitors';
    dune_writeData('Bene Gesserit made their prediction.');
    dune_postForum('Bene Gesserit made their prediction.', true);
    return;
}

function setupAction_chooseTraitors() {
    global $game, $info;
    dune_readData();
    // Checks input.
    
    // Carry Out Action.
    $game[$_SESSION['faction']]['traitors'] 
            = array($game['traitorDeck'][$_SESSION['faction']][(int)$_POST['traitor']]);
    $game['round']['choseTraitors'][$_SESSION['faction']] = true;
    $game['meta']['next'][$_SESSION['faction']] = 'wait';
    $isDone = true;
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
        if ($game['round']['choseTraitors'][$faction] == false) {
            $isDone = false;
        }
    }
    if ($isDone) {
        $game['meta']['next']['[F]'] = 'setupTokens';
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
    $message = $info['factions'][$_SESSION['faction']]['name'].' chose their traitor.';
    dune_writeData($message);
    dune_postForum($message, true);
    return;
}

function setupAction_setupTokens() {
    global $game, $info;
    if ($_SESSION['faction'] == '[F]') {
        // Checks input.
        if (($_POST['st'] + $_POST['stStar'] + $_POST['fww'] 
                        + $_POST['fwwStar'] + $_POST['fws'] 
                        + $_POST['fwsStar']) != 10) {
            return; //'<script>alert("Action failed.");</script>';
        }
        if (($_POST['stStar'] + $_POST['fwwStar'] 
                        + $_POST['fwsStar']) > 3) {                    
            return; //'<script>alert("Action failed.");</script>';       
        }
        if (($_POST['stStar'] + $_POST['fwwStar'] 
                        + $_POST['fwsStar']) < 0) {                    
            return;
        }
        // Carry out action.
        dune_readData();
        dune_gmMoveTokens('[F]', $_POST['st'], $_POST['stStar'], '[OFF]', '[ST]');
        dune_gmMoveTokens('[F]', $_POST['fww'], $_POST['fwwStar'], '[OFF]', $_POST['fwwSector']);
        dune_gmMoveTokens('[F]', $_POST['fws'], $_POST['fwsStar'], '[OFF]', $_POST['fwsSector']);
        $game['meta']['next']['[F]'] = 'wait';
        $game['meta']['next']['[B]'] = 'setupTokens';
        dune_writeData('Fremen places 10 starting tokens');
        dune_postForum('Fremen place starting tokiens.', true);
        return;
    }
    if ($_SESSION['faction'] == '[B]') {
        dune_readData();
        dune_gmMoveTokens('[B]', 1, 0, '[PS]', $_POST['token_loc']);
        $game['meta']['next']['[B]'] = 'wait';
        dune_writeData('Bene Gesserit places starting token');
        dune_postForum('Bene Gesserit places starting tokien.', true);
        return;
    }
}

function setupAction_setupTreachery() {
    global $game, $info;
    dune_readData();
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
        dune_dealTreachery($faction);
    }
    dune_dealTreachery('[H]');
    dune_writeData('Treachery cards delt.', true);
    dune_postForum('Treachery cards delt.', true);
    return;
}
?>
