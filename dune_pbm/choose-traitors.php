<?php 
//Fremen Setup Tokens Script
//To be called by index.php.

// Forms ###########################################################
if (empty($_POST)){
    if ($_SESSION['faction'] != '[H]') {
        global $game, $info;
		echo 
		'<h3>Faction</h3>
        
		<form action="#" method="post">
			Choose your traitor: 
            <select name="traitor">
            <option value="0">'.
            $info['leaders'][$game['traitorDeck'][$_SESSION['faction']][0]]['name'].
            '</option>
            <option value="1">'.
            $info['leaders'][$game['traitorDeck'][$_SESSION['faction']][1]]['name'].
            '</option>
            <option value="2">'.
            $info['leaders'][$game['traitorDeck'][$_SESSION['faction']][2]]['name'].
            '</option>
            <option value="3">'.
            $info['leaders'][$game['traitorDeck'][$_SESSION['faction']][3]]['name'].
            '</option>
            </select>
			<input type="submit" value="Submit">
		</form>';
	}
}

// Actions ########################################################
if (!empty($_POST)){
    global $info, $game;
    if (isset($_POST['traitor']) && ($_SESSION['faction'] != '[H]')) {
        // Carry out.
        dune_readData();
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
        }
        dune_writeData();            
        
        echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
    }
}
?>
