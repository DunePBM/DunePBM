<?php 
// Nexus
// Called from spice-round.php

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
	echo 
	'<h2>Nexus</h2>';

    echo
    '<p>A nexus has occoured. Form alliences.
    The nexus will not end until everyone selects "done"</p>
    <form action="" method="post">
    <button name="nexus_action" value="done">Done with Nexus</button>
    </form>';
}

if (isset($_POST['nexus_action'])) {
    if ($_POST['nexus_action'] == 'done') {
        $game['meta']['next'][$_SESSION['faction']] = 'wait.php';
        refreshPage();
    }        
}
?>
