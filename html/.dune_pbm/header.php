<?php 
// Game Header
// Called in index.php.

//######################################################################
//###### Forms -- Runs Every Time -- Even With Post Data ###############
//######################################################################

global $game, $info, $gmCommands;
echo '<h2>Faction: '.$info['factions'][$_SESSION['faction']]['name'].'</h2>';
echo
'<form action="#" method="post">
Pages:  <select name="header_action">
    <option value="home">Player Action</option>			
    <option value="forum">Forum</option>			
    <option value="mail">Mail</option>
    <option value="revealedPost">Revealed Post</option>
    <option value="status">Get Status</option>			
    <option value="board">Display Board</option>			
    <option value="special-commands">Special Commands</option>    
    <option value="logout">Logout</option>';
    if ($gmCommands == true) {
        echo '<option value=""></option>
            <option value="gm-commands">GM Commands</option>
            <option value="refresh">Refresh</option>';
    }
    echo 
    '</select> 
    <input type="submit" value="Go">
    </form><hr>';
	
	if (!isset($_SESSION['override']) && (!empty($game[$_SESSION['faction']]['alert']))) {
		$_SESSION['override'] = 'alert.php';
	}


//######################################################################
//###### Post ##########################################################
//######################################################################
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
    if ($_POST['header_action'] == 'revealedPost') {
        global $game;
        $_SESSION['override'] = 'revealed-post.php';
        refreshPage();
    }
    if ($_POST['header_action'] == 'board') {
        global $game;
        $_SESSION['override'] = 'board.php';
        refreshPage();
    }
    if ($_POST['header_action'] == 'status') {
        global $game;
        $_SESSION['override'] = 'status.php';
        refreshPage();
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
