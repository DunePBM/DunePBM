<?php 
//Fremen Setup Trechery.
//To be called from setup-tokens.php.

dune_readData();
foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
    dune_deal($treachery, $faction);
}
dune_deal($treacheryDeck, '[H]');

$game['meta']['event'] = 'Treachery cards delt.';
$game['meta']['faction'] = '';
foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
    $game['meta']['next'][$faction] = 'wait.php';
}
dune_writeData();            
echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
?>
