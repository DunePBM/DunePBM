<?php 
// Setup Treachery
// Called from setup-tokens.php
// setup-tokens.php --> setup-treachery.php --> storm.php

dune_readData();
foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
    dune_dealTreachery($faction);
}
dune_dealTreachery('[H]');

$game['meta']['event'] = 'Treachery cards delt.';
$game['meta']['faction'] = '';
foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
    $game['meta']['next'][$faction] = 'wait.php';
}
dune_writeData();            
echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
?>
