<?php 
// Game Header
// Called in index.php.

echo
'<form action="#" method="post">
Actions:  <select name="header_action">
    <option value="status">Get Status</option>			
    <option value="undo">Undo Last Move</option>
    <option value="gm-commands">GM Commands</option>
    <option value="special-commands">Special Commands</option>    
    <option value="logout">Logout</option>			
    
    <option value="dump">Dump Data</option>			
    <option value="reset">Reset Game</option>			
    <option value="refresh">Refresh</option>			
    <option value="home">Home</option>			
    
</select> 
<input type="submit" value="Submit">
</form>';

if (isset($_POST['header_action'])) {
    if ($_POST['header_action'] == 'logout') {
        session_destroy();
        refreshPage();
    }
    if ($_POST['header_action'] == 'reset') {
        dune_setupGame();
        refreshPage();
    }
    if ($_POST['header_action'] == 'status') {
        dune_printStatus($_SESSION['faction']);
    }
    if ($_POST['header_action'] == 'dump') {
        global $game;
        print '<pre>';
        print json_encode($game, JSON_PRETTY_PRINT);
        print_r($_SESSION);
        print_r($_POST);
        print '</pre>';
    }
    if ($_POST['header_action'] == 'gm-commands') {
        global $game;
        $_SESSION['override'] = 'gm-commands.php';
        refreshPage();
    }
    if ($_POST['header_action'] == 'special-commands') {
        global $game;
        $_SESSION['override'] = 'special-commands.php';
        refreshPage();
    }
    if ($_POST['header_action'] == 'undo') {
        dune_undoMove();
        refreshPage();
    }
    if ($_POST['header_action'] == 'refresh') {
        echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
    }
    if ($_POST['header_action'] == 'home') {
        unset($_SESSION['override']);
        refreshPage();
    }
}
?>
