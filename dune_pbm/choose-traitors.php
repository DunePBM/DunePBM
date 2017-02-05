<?php 
// Choose Traitors
// Called by index.php.
// make-prediction.php --> choose-traitors.php --> setup-tokens.php

// Forms ###########################################################
if (empty($_POST)){
    if ($_SESSION['faction'] != '[H]') {
        global $game, $info;
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

// Action #######################################################
if (!empty($_POST)){
    global $info, $game;
    if (isset($_POST['traitor']) && ($_SESSION['faction'] != '[H]')) {
        echo actionFunction();
        //echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
    }
}

function actionFunction() {
    global $game, $info;
    dune_readData();
    // Checks input.
    
    // Carry Out Action.
    $game[$_SESSION['faction']]['traitors'] 
            = array($game['traitorDeck'][$_SESSION['faction']][(int)$_POST['traitor']]);
    $game['meta']['event'] = $info['factions'][$_SESSION['faction']]['name'].' chose their traitor.';
    $game['meta']['faction'] = $_SESSION['faction'];
    $game['meta']['next'][$_SESSION['faction']] = 'wait.php';
    $t = true;
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
        if ($game['meta']['next'][$faction] != 'wait.php') {
            $t = false;
        }
    }
    if ($t) {
        $game['meta']['next']['[F]'] = 'setup-tokens.php';
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
    dune_writeData(); 
    return '<script>alert("Action successful.");</script>';
}
?>
