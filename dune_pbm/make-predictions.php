<?php 
// Bene Gesserit make their predicitons.
// To be called by index.php.

// Forms ###########################################################
if (empty($_POST)){
    if ($_SESSION['faction'] == '[B]') {
		echo 
		'<h3>Bene Gesserit:</h3>
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
