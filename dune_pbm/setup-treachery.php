<?php 
// Setup Treachery
// Called from setup-tokens.php
// setup-tokens.php --> setup-treachery.php --> storm-round.php

global $data, $info, $debug;
dune_readData();
foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
    dune_dealTreachery($faction);
}
dune_dealTreachery('[H]');

dune_postForum('Treachery cards delt.', true);
foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
    $game['meta']['next'][$faction] = 'spice-round.php';
}
dune_writeData('Treachery cards delt.', true);
refreshPage();
?>
