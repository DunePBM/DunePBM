<?php 
// Game Header
// Called in index.php.

echo '<h2>Faction: '.$info['factions'][$_SESSION['faction']]['name'].'</h2>';

echo
'<form action="#" method="post">
Actions:  <select name="header_action">
    <option value="home">Take Action</option>			
    <option value="forum">Forum</option>			
    <option value="mail">Mail</option>			
    <option value="status">Get Status</option>			
    <option value="special-commands">Special Commands</option>    
    <option value="logout">Logout</option>			
    <option value=""></option>
    <option value="gm-commands">GM Commands</option>
    <option value="refresh">Refresh</option>			
</select> 
<input type="submit" value="Submit">
</form>';




if (isset($_POST['header_action'])) {
    if ($_POST['header_action'] == 'logout') {
        session_destroy();
        refreshPage();
    }
    if ($_POST['header_action'] == 'forum') {
        global $game;
        $_SESSION['override'] = 'forum.php';
        refreshPage();
    }
    if ($_POST['header_action'] == 'mail') {
        global $game;
        $_SESSION['override'] = 'mail.php';
        refreshPage();
    }
    if ($_POST['header_action'] == 'status') {
        dune_printStatus($_SESSION['faction']);
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
