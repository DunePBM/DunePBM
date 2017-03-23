<?php 
// GM Commands
// Called from index.php
// uses $_SESSION['override']

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
	echo 
	'<h2>GM Commands</h2>';

    echo
    '<form action="#" method="post">
    Actions:  <select name="gm_action">
        <option value="dump">Dump Data</option>			
        <option value="reset">Reset Game</option>			
    </select> 
    <input type="submit" value="Submit">
    </form>';
}

if (isset($_POST['gm_action'])) {
    if ($_POST['gm_action'] == 'reset') {
        dune_setupGame();
        print '<script>alert("Game reset.");</script>';
        refreshPage();
    }
    if ($_POST['gm_action'] == 'dump') {
        global $game;
        print '<pre>';
        print json_encode($game, JSON_PRETTY_PRINT);
        print_r($_SESSION);
        print_r($_POST);
        print '</pre>';
    }
}
?>
