<?php 
//Login Script
//To be called by index.php.
global $game, $info;
print '<p><b><u>We are waiting for: </u></b><br>';
foreach (array_keys($game['meta']['next']) as $x) {
    if ($game['meta']['next'][$x] != 'wait.php') {
        print $info['factions'][$x]['name'].'<br>';
    }
}
    
?>
