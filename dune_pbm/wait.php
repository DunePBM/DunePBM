<?php 
//Login Script
//To be called by index.php.
global $game, $info;
print '<p>We are waiting for: <br>';
foreach (array_keys($game['meta']['next']) as $x) {
    if ($game['meta']['next'][$x] != 'wait.php') {
        print $info['factions'][$x]['name'].'<br>';
    }
}
    
?>
