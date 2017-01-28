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
	if ($_SESSION['faction'] == '[H]') {
		echo 
		'<h3>Harkonnen:</h3>
        Your traitors are: . . .';
	}
}

// Actions ########################################################
if (!empty($_POST)){
    if (isset($_POST['st']) && ($_SESSION['faction'] == '[F]')) {
        //Check post.
        if (($_POST['st'] + $_POST['stStar'] + $_POST['fww'] 
                    + $_POST['fwwStar'] + $_POST['fws'] 
                    + $_POST['fwsStar'] == 10) &&
                    ($_POST['stStar'] + $_POST['fwwStar'] 
                    + $_POST['fwsStar'] <= 3)) {                    
            //Carry out.
            dune_readData();
            dune_gmMove('[F]', $_POST['st'], $_POST['stStar'], '[OFF]', '[ST]');
            dune_gmMove('[F]', $_POST['fww'], $_POST['fwwStar'], '[OFF]', $_POST['fwwSector']);
            dune_gmMove('[F]', $_POST['fws'], $_POST['fwsStar'], '[OFF]', $_POST['fwsSector']);
            $game['meta']['event'] = 'Fremen places starting tokens';
            $game['meta']['faction'] = '[F]';
            $game['meta']['next']['[F]'] = 'wait.php';
            dune_writeData();            
            echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
        }
    }

    if (isset($_POST['token_loc']) && ($_SESSION['faction'] == '[B]')) {
        dune_readData();
        dune_gmMove('[B]', 1, 0, '[PS]', $_POST['token_loc']);
        $game['meta']['event'] = 'Bene Gesserit placees starting token';
        $game['meta']['faction'] = '[B]';
        $game['meta']['next']['[B]'] = 'wait.php';
        dune_writeData();
        echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
    }
}
?>
