<?php 
// Nexus
// Called from spice-round.php

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info;
	

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
