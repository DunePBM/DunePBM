<?php 
// Nexus
// Called from spice-round.php

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
	echo 
	'<h2>Nexus</h2>';

    echo
    'A nexus has occoured. Form alliences.
    The nexus will not end until everyone selects DONE.<br><br>
    There were sandworms in: <br>';
    foreach ($game['nexus']['sandworms'] as $x) {
        print $info['spiceDeck'][$x]['name'].'<br>';
    }
    echo
    '<br><form action="" method="post">
    <button name="nexus_action" value="done">Done with Nexus</button>
    </form>';
}

if (isset($_POST['nexus_action'])) {
    if ($_POST['nexus_action'] == 'done') {
        dune_readData();
        $game['meta']['next'][$_SESSION['faction']] = 'wait.php';
        $nexusOver = true;
        foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
            if ($game['meta']['next'][$faction] == 'nexus.php') {
                $nexusOver = false;
            }
        }
        if ($nexusOver == true) {
            unset($game['nexus']);
        }
        dune_writeData();
        refreshPage();
    }        
}
?>
