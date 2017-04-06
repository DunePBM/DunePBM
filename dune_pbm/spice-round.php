<?php 
// The spice round.
// Called by index.php.
// storm-round.php --> spice-round.php --> bidding-round.php

// Runs the first time the page is called. 
if (!isset($game['spiceRound'])) {
    unset($game['nexus']);
    global $game, $info;
    $game['spiceRound'] = array();
    // Double spice blow.
    for ($i = 1; $i <= 2; $i += 1) {
        dune_dealSpice($i);
        $overCard = $game['spiceDeck']['discard-'.$i][0];
        /*$underCard = "";
        
        while ($info['spiceDeck'][$overCard]['type'] == 'worm') {
            if (!isset($game['nexus'])) {
                $game['nexus'] = array();
                $game['nexus']['sandworms'] = array();
            }
            array_push($game['nexus']['sandworms'], $underCard);
            foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
                $game['meta']['next'][$faction] = 'nexus.php';
            }
            $underCard = $overCard;
            dune_dealSpice($i);
            $overCard = $game['spiceDeck']['discard-'.$i][0];
        }*/
        // Deals spice.
        $game['spiceRound']['spice-'.$i]['location'] = $overCard;
        $game['spiceRound']['spice-'.$i]['spice'] = $info['spiceDeck'][$overCard]['spice'];
        array_shift($game['spiceDeck']['discard-'.$i]);
        dune_writeData('Spice Card #'.$i, true);
    }
    if (isset($game['nexus'])) {
        $temp = 'A nexus has occoured. There were sandworms in: ';
        foreach ($game['nexus']['sandworms'] as $x) {
            $temp .= $info['spiceDeck'][$x]['name'];
            $temp .= ', ';
        }
        $temp = substr($temp, 0, -2);
        dune_postForum($temp, true);
    }
}

// If a nexus is occouring.
if (isset($game['nexus'])) {
    if ($game['meta']['next'][$_SESSION['faction']] == 'spice-round.php') {
        dune_getWaiting();
    }
    else {
        refreshPage();
    }
}

// If a nexus does not occour or is finished.
if ((isset($game['spiceRound'])) && (!isset($game['nexus']))) {
    array_unshift($game['spiceDeck']['discard-1'], $game['spiceRound']['spice-1']['location']);
    array_unshift($game['spiceDeck']['discard-2'], $game['spiceRound']['spice-2']['location']);
    
    $game['tokens'][$game['spiceRound']['spice-1']['location']]['[SPICE]'][0] = (int)$game['spiceRound']['spice-1']['spice'];
    $game['tokens'][$game['spiceRound']['spice-1']['location']]['[SPICE]'][1] = 0;

    $game['tokens'][$game['spiceRound']['spice-2']['location']]['[SPICE]'][0] = (int)$game['spiceRound']['spice-2']['spice'];
    $game['tokens'][$game['spiceRound']['spice-2']['location']]['[SPICE]'][1] = 0;
    
    //dune_gmMoveTokens('[SPICE]', (int)$game['spiceRound']['spice-1']['spice'], 
    //                    0, '[BANK]', $game['spiceRound']['spice-1']['location']);
    //dune_gmMoveTokens('[SPICE]', (int)$game['spiceRound']['spice-2']['spice'], 
    //                    0, '[BANK]', $game['spiceRound']['spice-2']['location']);
    
    $temp = 'Spice Blooms on ';
    $temp .= $info['spiceDeck'][$game['spiceRound']['spice-1']['location']]['name'];
    $temp .= ' ('.$info['spiceDeck'][$game['spiceRound']['spice-1']['location']]['spice'].') and ';
    $temp .= $info['spiceDeck'][$game['spiceRound']['spice-2']['location']]['name'];
    $temp .= ' ('.$info['spiceDeck'][$game['spiceRound']['spice-2']['location']]['spice'].') ';
    dune_postForum($temp, true);
    unset($game['spiceRound']);
    dune_writeData();
}
?>
