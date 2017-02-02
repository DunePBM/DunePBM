<?php 
// Setup Tokens
// Called by index.php.
// choose-traitors.php --> setup-tokens.php --> setup-treachery.php

// Forms ###########################################################
if (empty($_POST)){
    if ($_SESSION['faction'] == '[F]') {
		echo 
		'<h3>Fremen:</h3>
		<form action="#" method="post">
			Place <input id="st" name="st" type="number" min=0 max=10 value="0"/> /
			<input id="stStar" name="stStar" type="number" min=0 max=3 value="0"/> 
			* in Sietch Tabr.<br>
			
			Place <input id="fww" name="fww" type="number" min=0 max=10 value="0"/> /
			<input id="fwwStar"  name="fwwStar" type="number" min=0 max=3 value="0"/> 
			* on <select name="fwwSector">
            <option value="[FWW-1]">False Wall West &lt16&gt</option>
            <option value="[FWW-2]">False Wall West &lt17&gt</option>
            <option value="[FWW-3]">False Wall West &lt18&gt</option>
            </select><br>
            
            Place <input id="fws"  name="fws" type="number" min=0 max=10 value="0"/> /
			<input id="fwsStar"  name="fwsStar" type="number" min=0 max=3 value="0"/> 
			* on <select name="fwsSector">
            <option value="[FWS-1]">False Wall South &lt4&gt</option>
            <option value="[FWS-2]">False Wall South &lt5&gt</option>
            </select><br>
            
			<input type="submit" value="Submit">
		</form>';
	}
	if ($_SESSION['faction'] == '[B]') {
		echo 
		'<h3>Bene Gesserit:</h3>';
        getTerritory('Select your token location: ', 'token_loc', true);
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
            $game['meta']['next']['[B]'] = 'setup-tokens.php';
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
        
        include 'setup-treachery.php';
        
        echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
    }
}
?>
