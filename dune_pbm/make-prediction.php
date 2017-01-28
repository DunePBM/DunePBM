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
    if (isset($_POST['winningFaction']) && 
                    isset($_POST['winningTurn']) &&
                    $_SESSION['faction'] == '[F]') {
        $game['[B]']['prediction']['winningFaction'] = $_POST['winningFaction'];
        $game['[B]']['prediction']['winningTurn'] = $_POST['winningTurn'];        
        $game['meta']['event'] = 'Fremen places starting tokens';
        $game['meta']['faction'] = '[F]';
        $game['meta']['next']['[F]'] = 'wait.php';
        dune_writeData();            
        echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
    }
}
?>
