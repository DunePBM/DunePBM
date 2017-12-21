<?php 
// Get Alerts
// Called from index.php
// uses $_SESSION['override']

global $game, $info;

//######################################################################
//###### Forms #########################################################
//######################################################################

dune_readData();
if (!empty($game[$_SESSION['faction']]['alert'])) {
	echo '<h3>Game Alerts:</h3>';
	foreach ($game[$_SESSION['faction']]['alert'] as $alert) {
		echo $alert.'<br>';
	}
}	
    
echo
'<br><form action="" method="post">
<button name="alertAction" value="done">Done</button>
</form>';

//######################################################################
//###### Post ##########################################################
//######################################################################
if (isset($_POST['alertAction'])) {
    if ($_POST['alertAction'] == 'done') {
        dune_readData();
        $game[$_SESSION['faction']]['alert'] = array();
        unset($_SESSION['override']);
        dune_writeData($_SESSION['faction'].' is done with alerts.');
    }
    refreshPage();
}

//######################################################################
//###### Actions #######################################################
//######################################################################
?>
