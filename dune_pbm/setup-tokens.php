<?php 
// Setup Tokens
// Called by index.php.
// choose-traitors.php --> setup-tokens.php --> setup-treachery.php

//######################################################################
//###### Forms #########################################################
//######################################################################
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

//######################################################################
//###### Post ##########################################################
//######################################################################
if (!empty($_POST)){
    if (isset($_POST['st']) && ($_SESSION['faction'] == '[F]')) {
        actionFunctionF();
        refreshPage();
    }

    if (isset($_POST['token_loc']) && ($_SESSION['faction'] == '[B]')) {
        actionFunctionB();
        refreshPage();
    }
}

//######################################################################
//###### Actions #######################################################
//######################################################################
function actionFunctionF() {
    global $game, $info;
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
        return; //'<script>alert("Action failed.");</script>';       
    }
    // Carry out action.
    dune_readData();
    dune_gmMoveTokens('[F]', $_POST['st'], $_POST['stStar'], '[OFF]', '[ST]');
    dune_gmMoveTokens('[F]', $_POST['fww'], $_POST['fwwStar'], '[OFF]', $_POST['fwwSector']);
    dune_gmMoveTokens('[F]', $_POST['fws'], $_POST['fwsStar'], '[OFF]', $_POST['fwsSector']);
    $game['meta']['event'] = 'Fremen places starting tokens';
    $game['meta']['faction'] = '[F]';
    $game['meta']['next']['[F]'] = 'wait.php';
    $game['meta']['next']['[B]'] = 'setup-tokens.php';
    dune_writeData();
    return; //'<script>alert("Action successful.");</script>';       
}

function actionFunctionB() {
    global $game, $info;
    dune_readData();
    dune_gmMoveTokens('[B]', 1, 0, '[PS]', $_POST['token_loc']);
    $game['meta']['event'] = 'Bene Gesserit placees starting token';
    $game['meta']['faction'] = '[B]';
    $game['meta']['next']['[B]'] = 'wait.php';
    dune_writeData();
    include 'setup-treachery.php';
    return; //'<script>alert("Action successful.");</script>';        
}
?>
