<?php 
// Get Status
// Called from index.php
// uses $_SESSION['override']

//######################################################################
//###### Forms #########################################################
//######################################################################
dune_printStatus($_SESSION['faction']);
print '<br><hr>';
if ($game['meta']['next'][$_SESSION['faction']] != 'wait') {
    dune_getWaiting();
}

//######################################################################
//###### Actions #######################################################
//######################################################################
        
function dune_printStatus($faction) {
    global $gameDir, $game, $info;
    print '<h3>Game Status:</h3>';

    // Long Player Order 
    //print '<p><b><u>Player Order</u>:</b><br>';
    //$textTemp = '';
    //foreach ($game['meta']['playerOrder'] as $faction) {
    //    $textTemp .= ' '.$info['factions'][$faction]['name'].
    //    ' (Dot '.$game['meta']['playerDots'][$faction].')<br>';
    //}
    //print $textTemp.'</p>';

    // Player Order
    print '<p><b><u>Player Order</u>:</b> ';
    $textTemp = '';
    foreach ($game['meta']['playerOrder'] as $faction) {
        $textTemp .= $faction.', ';
    }
    print substr($textTemp, 0, -2).'</p>';
    // The Storm
    print '<b><u>Storm</u>:</b> ';
    if ($game['storm']['location'] == 0) {
        print 'The storm has not been placed yet.<br><br>';
    } else {
        print 'The storm is in Sector '.$game['storm']['location'].'.<br><br>';
    }
    // Spice Treasury
    print '<b><u>Spice Treasury </b>(Hidden)<b></u>:</b> ';
    print $game[$faction]['spice'].' spice.<br><br>';
    // Show Tokens & Spice
    print '<b><u>Token & Spice Locations</u>:</b><br><br style="line-height: 6px"/>';
    foreach (array_diff(array_keys($game['tokens']), array('[OFF]', '[BANK]')) as $y) {
        print '<u>'.explode(' (', $info['territory'][$y]['name'])[0];
        if (isset($info['territory'][$y]['sector'])) {
            print ' (Sector '.$info['territory'][$y]['sector'].')';
        }
        print ':</u><br>';
        foreach (array_keys($game['tokens'][$y]) as $x) {
            if ($x == '[SPICE]') {
                print $info['factions'][$x]['name'].': '.$game['tokens'][$y][$x][0];
            }
            else {
                print $info['factions'][$x]['name'].': '.$game['tokens'][$y][$x][0];
                if ($game['tokens'][$y][$x][1] != 0) {
                    if ($x != '[B]') {
                        print '/'.$game['tokens'][$y][$x][1].'*';
                    }
                    if ($x == '[B]') {
                        print ' (coexisting)';
                    }
                }
            }
        
            print '<br>';
        }
        print '<br style="line-height: 6px"/>';
    }
    print '<br>';
    // Leaders
    
    
    // Traitors
    print '<b><u>Traitors </b>(Hidden)<b></u>:</b><br>';
    if (empty($game[$faction]['traitors'])) {
        print 'None<br>';
    }
    else {
        foreach ($game[$faction]['traitors'] as $y) {
            print $info['leaders'][$y]['name'].'<br>';
        }
    }
    print '<br>';
    // Treachery
    print '<b><u>Treachery </b>(Hidden)<b></u>:</b><br>';
    if (empty($game[$faction]['treachery'])) {
        print 'None<br>';
    }
    else {
        foreach ($game[$faction]['treachery'] as $y) {
            print $info['treachery'][$y]['name'].'<br>';
        }
    }
    print '<br>';
    // Treachery Discards
    print '<b><u>Treachery Discards</u>:</b><br>';
    if (empty($game['treacheryDeck']['discard'])) {
        print 'None<br>';
    }
    else {
        foreach ($game['treacheryDeck']['discard'] as $y) {
            print $info['treachery'][$y]['name'].'<br>';
        }
    }
    print '<br>';
    // Spice Discards
    print '<b><u>Spice Discards #1</u>:</b><br>';
    if (empty($game['spiceDeck']['discard-1'])) {
        print 'None<br>';
    }
    else {
        foreach ($game['spiceDeck']['discard-1'] as $y) {
            print $info['spiceDeck'][$y]['name'].'<br>';
        }
    }
    print '<br>';
    print '<b><u>Spice Discards #2</u>:</b><br>';
    if (empty($game['spiceDeck']['discard-2'])) {
        print 'None<br>';
    }
    else {
        foreach ($game['spiceDeck']['discard-2'] as $y) {
            print $info['spiceDeck'][$y]['name'].'<br>';
        }
    }
    print '<br>';
    // Notes
    print '<b><u>Notes </b>(Hidden)<b></u>:</b><br>';
    if (empty($game[$faction]['notes'])) {
        print 'None';
    }
    else {
        foreach ($game[$faction]['notes'] as $y) {
            print $y.'<br>';
        }
    }
}
?>
