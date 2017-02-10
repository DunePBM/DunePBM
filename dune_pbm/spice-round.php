<?php 
// The spice round.
// Called by index.php.
// storm-round.php --> spice-round.php --> bidding-round.php

// Forms ###########################################################
$global $game, $info;

// Checks for a nexus on card 1
while (dune_checkSpice() == 'Shai-Hulud') {
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
    $game['meta']['next'][$faction] = 'nexus.php';
    $game['nexus']['sandworm'][] = $game['spice_deck']['discard-1'][0];
    dune_dealSpice('discard-1');
}
dune_dealSpice('discard-1');
$game['spice_round']['spice1']['location'] = $game['spice_deck']['discard-1'];
$game['spice_round']['spice1']['spice'] = $info[[$game['spice_deck']['discard-1']]['spice'];

// Checks for a nexus on card 2
while (dune_checkSpice() == 'Shai-Hulud') {
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
    $game['meta']['next'][$faction] = 'nexus.php';
    $game['sandworm'][] = $game['spice_deck']['discard-2'][0];
    dune_dealSpice('discard-2');
}
dune_dealSpice('discard-2');
$game['spice_round']['spice2']['location'] = $game['spice_deck']['discard-2'];
$game['spice_round']['spice2']['spice'] = $info[[$game['spice_deck']['discard-2']]['spice'];

// If a nexus occours.
if (isset($game['nexus'])) {
    $game['nexus']['spice1'] = $game['spice_round']['spice1'];
    $game['nexus']['spice2'] = $game['spice_round']['spice2'];
    unset($game['spice_round']);
    if (!$debug) {
        echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
    }
}

// If a nexus does not occour.
else {
    $game['tokens'][$game['spice_round']['spice1']['location']]['Spice'] =
                            $game['spice_round']['spice1']['spice'];
    $game['tokens'][$game['spice_round']['spice2']['location']]['Spice'] =
                            $game['spice_round']['spice2']['spice'];
    echo
    '
    
    <FORM>
    <INPUT TYPE="button" onClick="history.go(0)" VALUE="Refresh">
    </FORM>';
    unset($game['spice_round']);
}

?>
