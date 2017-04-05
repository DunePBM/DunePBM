<?php 
// The spice round.
// Called by index.php.
// storm-round.php --> spice-round.php --> bidding-round.php

// Runs the first time the page is called. 
if (!isset($game['spiceRound'])) {
    global $game, $info;
    
    $game['spiceRound'] = array();
    // Checks for sandworms.
    for ($i = 1; $i <= 2; $i += 1) {
        dune_dealSpice($i);
        $x = $game['spiceDeck']['discard-'.$i][0];
        print $x;
        $xShort = explode('-', $x)[0];
        while ($xShort == '[WORM') {
            print 'hit';
            if (!isset($game['nexus'])) {
                $game['nexus'] = array();
                $game['nexus']['sandworms'] = array();
            }
            array_push($game['nexus']['sandworms'], $game['spiceDeck']['discard-'.$i][0]);
            foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
                $game['meta']['next'][$faction] = 'nexus.php';
            }
            dune_dealSpice($i);
        }
        $game['spiceRound']['spice-'.$i]['location'] = $x;
        $game['spiceRound']['spice-'.$i]['spice'] = $info['spiceDeck'][$x]['spice'];
        dune_dealSpice($i);
    }
    
    // If a nexus occours.
    if (isset($game['nexus'])) {
        refreshPage();
    }
}

// If a nexus does not occour.
if (isset($game['spiceRound'])) {
    dune_gmMoveTokens('[SPICE]', $game['spiceRound']['spice-1']['spice'], 
                        0, '[BANK]', $game['spiceRound']['spice-1']['location']);
    dune_gmMoveTokens('[SPICE]', $game['spiceRound']['spice-2']['spice'], 
                        0, '[BANK]', $game['spiceRound']['spice-2']['location']);
    dune_postForum('Spice Blooms on . . . ', true);
    unset($game['spiceRound']);
}
?>
