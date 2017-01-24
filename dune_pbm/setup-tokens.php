<?php 
	//Fremen Setup Tokens Script
	//To be called by index.php.

	if ($_SESSION['faction'] == '[F]') {
		echo 
		'<h3>Fremen:</h3>
		<form action="#" method="post">
			Place <input id="st" name="st" type="number" min=0 max=10 value="0"/> /
			<input id="stStar" name="stStar" type="number" min=0 max=3 value="0"/> 
			* in Sietch Tabr.<br>
			
			Place <input id="fww" name="fww" type="number" min=0 max=10 value="0"/> /
			<input id="fwsStar"  name="fwsStar" type="number" min=0 max=3 value="0"/> 
			* on False Wall West in sector 
			<input id="fwsSector" name="fwsSector" type="number" min=16 max=18 value="18"/>. <br>
			
			Place <input id="fws"  name="fws" type="number" min=0 max=10 value="0"/> /
			<input id="fweStar"  name="fweStar" type="number" min=0 max=3 value="0"/> 
			* on False Wall South in sector 
			<input id="fweSector" name="fweSector" type="number" min=4 max=5 value="5"/>. <br>
			
			<input type="submit" value="Submit">
		</form>';
	}
	if ($_SESSION['faction'] == '[B]') {
		echo 
		'<h3>Bene Gesserit:</h3>'
        getTerritory('Select your token location: ', 'token_loc', true);
	}

    if (isset($_POST['st']) && ($_SESSION['faction'] == '[F]')) {
        //Check post.
        
        
        //Carry out.
        dune_readData();
        dune_gmMove('[F]', $_POST['st'], $_POST['stStar'], '[OFF]', '[ST]');
        dune_gmMove('[F]', $_POST['fww'], $_POST['fwwStar'], '[OFF]', '[FWW]');
        dune_gmMove('[F]', $_POST['fws'], $_POST['fwsStar'], '[OFF]', '[FWS]');
        $game['meta']['event'] = 'Fremen places starting tokens';
        $game['meta']['faction'] = '[F]';
        dune_writeData();
    }

    if (isset($_POST['token_loc']) && ($_SESSION['faction'] == '[B]')) {
        dune_readData();
        dune_gmMove('[B]', 1, 0, '[PS]', $_POST['token_loc']);
        $game['meta']['event'] = 'Bene Gesserit placees starting token';
        $game['meta']['faction'] = '[B]';
        dune_writeData();
    }

    header('Location: index.php');
} 
?>
