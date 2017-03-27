<?php 
// The spice round.
// Called by index.php.
// storm-round.php --> spice-round.php --> bidding-round.php

// Runs the first time the page is called. 
if (!isset($game['spice_round'])) {
    global $game, $info;
    
    // Checks for sandworms.
    for ($i = 1; $i <= 2; $i += 1) {
        while (explode('-', $game['spice_deck']['deck'])[0] == '[WORM') {
            foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
            $game['nexus'] = true;
            $game['meta']['next'][$faction] = 'nexus.php';
            $game['nexus']['sandworm'][] = $game['spice_deck']['discard-'.$i][0];
            dune_dealSpice($i);
        }
        dune_dealSpice($i);
        $game['spice_round']['spice-'.$i]['location'] = $game['spice_deck']['discard-'.$i];
        $game['spice_round']['spice-'.$i]['spice'] = $info[############################[$game['spice_deck']['discard-'.$i]]['spice'];
    }
    
    // If a nexus occours.
    if (isset($game['nexus'])) {
        refreshPage();
    }
}

// If a nexus does not occour.
if (isset($game['spice_round'])) {
    $game['tokens'][$game['spice_round']['spice-1']['location']]['Spice'] =
                            $game['spice_round']['spice-1']['spice'];
    $game['tokens'][$game['spice_round']['spice-2']['location']]['Spice'] =
                            $game['spice_round']['spice-2']['spice'];
    echo
    'Spice Blooms 
    <form>
    <input type="button" onClick="history.go(0)" VALUE="Refresh">
    </form>';
    unset($game['spice_round']);
}
?>
