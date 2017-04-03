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
    '<form action="" method="post">
    <button name="gm_action" value="dump">Dump Data</button>
    </form>
    
    <form action="" method="post">
    <button name="gm_action" value="reset">Reset Game</button>
    </form>';
}

if (isset($_POST['gm_action'])) {
    if ($_POST['gm_action'] == 'reset') {
        dune_setupGame();
        unset($_SESSION['override']);
        unset($_SESSION['faction']);
        //print '<script>alert("Game reset.");</script>';
        refreshPage();
    }
    if ($_POST['gm_action'] == 'dump') {
        global $game;
        print '<pre>';
        print '<br><b>Dune Data</b><br>';
        print json_encode($game, JSON_PRETTY_PRINT);
        print '<br><br><b>Dune Forum</b><br>';
        print json_encode($duneForum, JSON_PRETTY_PRINT);
        print '<br><br><b>Dune Mail</b><br>';
        print json_encode($duneMail, JSON_PRETTY_PRINT);
        print '<br><br><b>SESSION</b><br>';
        print_r($_SESSION);
        print '<br><br><b>POST</b><br>';
        print_r($_POST);
        print '</pre>';
    }
}
?>
