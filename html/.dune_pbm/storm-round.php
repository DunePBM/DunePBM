<?php 
// Storm Round
// Called from index.php
// setup-treachery.php --> storm-round.php --> spice-round.php
// colleciton-round.php --> storm-round.php --> spice-round.php
    
//##############################################################
//## First Run #################################################
//##############################################################
if (!isset($game['stormRound'])) {
    $game['stormRound'] = array();
    dune_writeData('Set up Storm Round', true);
    refreshPage();
}

if ($game['meta']['turn'] == 1) {
	foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
        $game['meta']['next'][$faction] = 'bidding-round.php';
        $game['faction']['alert'][] = 'The storm is in Sector '.$game['storm']['location'].'.';
    }
    dune_writeData('Done with storm-round.');
    refreshPage();
}        

//######################################################################
//## Every Round #######################################################
//######################################################################
if (isset($game['stormRound'])) {
    
    //##############################################################
    //## Checks for end of round. ##################################
    //##############################################################
    $isGameDone = true;
    foreach (array('[A]', '[B]', '[E]','[F]','[G]','[H]') as $faction) {
        if ($game['stormRound']['next'][$faction] != 'wait') {
            $isGameDune = false;
        }
    }
    if ($isGameDone) {
        setupAction_setupTreachery();
        setupAction_setupStorm();
        dune_readData();
        foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
            $game['meta']['next'][$faction] = 'spice-round.php';
        }
        unset($GLOBALS['game']['setupRound']);
        dune_writeData('Setup is over. The storm is placed. The Spice Round begins.', true);
        refreshPage();
    }
}


//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)){
    global $game, $info;

    
    //##############################################################
    //## Every Run #################################################
    //##############################################################
	echo 
	'<h2>Storm Round</h2>
    <p>The storm is in Sector '.$game['storm']['location'].'.</p>';
    
    if ($game['meta']['turn'] >= 2) {
        echo
        '<p>The storm will move '.$game['storm']['move'].' sectors.</p>';
        'You may play Weather Control or Faimly Atomics.';
    }
    
    echo
    '<br><form action="" method="post">
    <button name="stormAction" value="done">Done with Storm</button>
    </form>';
}

//######################################################################
//###### Post ##########################################################
//######################################################################
if (isset($_POST['stormAction'])) {
    if ($_POST['stormAction'] == 'done') {
        dune_readData();
        $game['meta']['next'][$_SESSION['faction']] = 'wait.php';
        dune_writeData('Done with storm-round.');
    }
        
    //##############################################################
    //## Checks for End of Round ###################################
    //##############################################################
    dune_checkRoundEnd('stormRound', 'bidding-round.php', 'Storm round has ended.');
    refreshPage();
}

//######################################################################
//###### Actions #######################################################
//######################################################################
?>
