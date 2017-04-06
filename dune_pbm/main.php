<?php
//Called by index.php.
$dataPath = '/var/www/dune_pbm_data/';
$game = "";
$duneForum = array();
$duneMail = array();
$debug = false;
$gmCommands = true;
$info = json_decode(file_get_contents($gamePath.'dune_info.json'), true);

function refreshPage() {
    global $debug;
    if (!$debug) {
        echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
        //Also Works:
        //$URL="http://yourwebsite.com/";
        //echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
    }
}

function dune_setupGame() {
    global $dataPath, $gamePath, $game, $info, $duneForum, $duneMail;
	$game = json_decode(file_get_contents($gamePath.'dune_data_start.json'), true);
    $duneForum = json_decode(file_get_contents($gamePath.'dune_forum_start.json'), true);
    $duneMail = json_decode(file_get_contents($gamePath.'dune_mail_start.json'), true);
    // Shuffle Player Dots
    shuffle($game['meta']['playerDots']);
    $game['meta']['playerOrder'] = $game['meta']['playerDots'];
    // Treachery Card Setup
    $treacheryDeck = array_keys($info['treachery']);
    shuffle($treacheryDeck);
    $game['treachery']['deck'] = $treacheryDeck;
    // Spice Card Setup
    $spiceDeckTemp = array_keys($info['spiceDeck']);
    shuffle($spiceDeckTemp);
    $game['spiceDeck']['deck-1'] = array();
    $game['spiceDeck']['deck-2'] = array();
    while ($info['spiceDeck'][$spiceDeckTemp[0]]['type'] == 'worm') {
        $spiceDeckTemp = array_cycle($spiceDeckTemp);
    }
    $spiceDeckTemp1 = array_slice($spiceDeckTemp, 0, 10);
    $spiceDeckTemp2 = array_slice($spiceDeckTemp, 10);
    while ($info['spiceDeck'][$spiceDeckTemp2[0]]['type'] == 'worm') {
        
        $spiceDeckTemp2 = array_cycle($spiceDeckTemp2);
    }
    $spiceDeckTemp = array_keys($info['spiceDeck']);
    shuffle($spiceDeckTemp);
    array_merge($spiceDeckTemp1, array_slice($spiceDeckTemp, 0, 11));
    array_merge($spiceDeckTemp2, array_slice($spiceDeckTemp, 11));
    $game['spiceDeck']['deck-1'] = $spiceDeckTemp1;
    $game['spiceDeck']['deck-2'] = $spiceDeckTemp2;
    // Traitor Setup
    $traitorDeck = array_keys($info['leaders']);
    shuffle($traitorDeck);
    $game['traitorDeck']['deck'] = $traitorDeck;
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
        for ($i = 0; $i <4; $i++) {
            array_unshift($game['traitorDeck'][$faction], array_shift($game['traitorDeck']['deck']));
        }
    }
    $game['[H]']['traitors'] = $game['traitorDeck']['[H]'];
    // Setup Storm
    $game['storm']['location'] = 0;
    $game['storm']['move'] = mt_rand(1, 18);
    dune_writeData();
    dune_writeForum();
    dune_writeMail();
}

function dune_readData($fm = true) {
	global $dataPath, $game, $duneForum, $duneMail;
	$file = $dataPath.'dune_data'; // eclude the extension.
	$game = json_decode(file_get_contents($file.'.json'), true);
    if ($fm) {
        dune_readForum();
        dune_readMail();
    }
    if (!isset($game)) {print 'ERROR REDING FILE';}
}

function dune_readForum() {
	global $dataPath, $game, $duneForum, $duneMail;
	$file = $dataPath.'dune_forum'; // eclude the extension.
    $duneForum = json_decode(file_get_contents($file.'.json'), true);
    if (!isset($game)) {print 'ERROR REDING FILE';}
    
}

function dune_readMail() {
	global $dataPath, $game, $duneForum, $duneMail;
    $file = $dataPath.'dune_mail'; // eclude the extension.
    $duneMail = json_decode(file_get_contents($file.'.json'), true);
    if (!isset($game)) {print 'ERROR REDING FILE';}
}

function dune_writeData($event='', $gm=false) {
	global $dataPath, $game, $duneForum, $duneMail;
    $maxUndo = 5;
	$file = $dataPath.'dune_data'; // eclude the extension.
	if (isset($game)) {
        // Setup undo move.
        for ($i = ($maxUndo - 1); $i >= 0; $i -= 1) {
            $undoGame = json_decode(file_get_contents
                            ($file.'.undo'.$i.'.json'), true);
            file_put_contents($file.'.undo'.($i + 1).'.json', 
                            json_encode($undoGame, JSON_PRETTY_PRINT));
        }
        $undoGame = json_decode(file_get_contents
                            ($file.'.json'), true);
        file_put_contents($file.'.undo0.json', 
                            json_encode($undoGame, JSON_PRETTY_PRINT));
        // Write new move.
        $game['meta']['eventNumber'] += 1;
        if ($event != '') {
            $game['meta']['event'] = $event;
        }
        $game['meta']['faction'] = $_SESSION['faction'];
        if ($gm) {
            $game['meta']['faction'] = '[DUNE]';
        }
        file_put_contents($file.'.json', json_encode($game, JSON_PRETTY_PRINT));
		file_put_contents($file.'.'.time().'.json', json_encode($game, JSON_PRETTY_PRINT));
	} else {print 'ERROR WRITING FILE';}
}

function dune_writeForum() {
	global $dataPath, $game, $duneForum, $duneMail;
    $file = $dataPath.'dune_forum'; // eclude the extension.
    file_put_contents($file.'.json', json_encode($duneForum, JSON_PRETTY_PRINT));
    file_put_contents($file.'.'.time().'.json', json_encode($game, JSON_PRETTY_PRINT));
}

function dune_writeMail() {
	global $dataPath, $game, $duneForum, $duneMail;
    $file = $dataPath.'dune_mail'; // eclude the extension.
    file_put_contents($file.'.json', json_encode($duneMail, JSON_PRETTY_PRINT));
    file_put_contents($file.'.'.time().'.json', json_encode($game, JSON_PRETTY_PRINT));
}

function dune_postForum($message, $gm = false) {
    global $game, $info, $duneForum, $duneMail;
    if (isset($_SESSION['faction'])) {
        dune_readForum();
        $dunePost = array();
        $dunePost['faction'] = $_SESSION['faction'];
        if ($gm) {
            $dunePost['faction'] = '[DUNE]';
        }
        $dunePost['time'] = (string) $game['meta']['eventNumber'].': '.(string) time();
        $dunePost['message'] = $message;
        array_push($duneForum, $dunePost);
    }
    dune_writeForum();
}

function dune_postMail($message, $toFaction, $gm=false) {
    global $game, $info, $duneForum, $duneMail;
    if (isset($_SESSION['faction'])) {
        dune_readMail();
        $dunePost = array();
        $dunePost['fromFaction'] = $_SESSION['faction'];
        if ($gm) {
            $dunePost['faction'] = '[DUNE]';
        }
        $dunePost['toFaction'] = $toFaction;
        $dunePost['message'] = $message;
        $dunePost['time'] = (string) $game['meta']['eventNumber'].': '.(string) time();
        array_push($duneMail[$toFaction]['inbox'], $dunePost);
        array_push($duneMail[$toFaction]['inbox'], $dunePost);
        if (!$gm) {
            array_push($duneMail[$_SESSION['faction']]['sent'], $dunePost);
        }
    }
    dune_writeMail();
}

function dune_undoMove() {
	global $dataPath, $game;
    $file = $dataPath.'dune_data'; // eclude the extension.
    $maxUndo = 5;
    //if ($game['meta']['faction'] == $_SESSION['faction']) {
    if (true) {
        $undoGame = json_decode(file_get_contents
                                ($file.'.undo0.json'), true);
        file_put_contents($file.'.json', 
                                json_encode($undoGame, JSON_PRETTY_PRINT));
        for ($i = 0; $i < $maxUndo; $i += 1) {
                $undoGame = json_decode(file_get_contents
                                ($file.'.undo'.($i +1).'.json'), true);
                file_put_contents($file.'.undo'.$i.'.json', 
                                json_encode($undoGame, JSON_PRETTY_PRINT));
        }
    }
}

function dune_gmMoveTokens($faction, $tokens, $starTokens, $fromLoc, $toLoc, $coexisting=false) {
	global $game;
    if (($tokens != 0) || ($starTokens != 0)) {
        if (!isset($game['tokens'][$fromLoc][$faction])) {
            $game['tokens'][$fromLoc][$faction] = [0,0];
        } if (!isset($game['tokens'][$toLoc][$faction])) {
            $game['tokens'][$toLoc][$faction] = [0,0];
        }
        $game['tokens'][$fromLoc][$faction][0] -= $tokens;
        $game['tokens'][$fromLoc][$faction][1] -= $starTokens;
        $game['tokens'][$toLoc][$faction][0] += $tokens;
        $game['tokens'][$toLoc][$faction][1] += $starTokens;
    }
	if ($game['tokens'][$fromLoc][$faction] == [0,0]) {
        unset($game['tokens'][$fromLoc][$faction]);
    }
    if (isset($game['tokens'][$fromLoc]['[B]'][0]) &&
        $game['tokens'][$fromLoc]['[B]'][0] == 0) {
        unset($game['tokens'][$fromLoc][$faction]);
    }
    if (empty($game['tokens'][$fromLoc])) {
        unset($game['tokens'][$fromLoc]);
    }
    if ($coexisting && $faction == '[B]') {
        $game['tokens'][$toLoc][$faction][1] = 1;
    }
}

function dune_shuffleTreachery() {
    global $game;
    $game['treachery']['deck'] = array_slice($game['treachery']['discard'], 2);
    $game['treachery']['discard'] = array_slice($game['treachery']['discard'], 0, 2);
    shuffle($game['treachery']['deck']);
    dune_writeData('Shuffled Treachery', true);
}
    
function dune_dealTreachery($toFaction) {
    global $game;
    if (empty($game['treachery']['deck'])) {
        dune_shuffleTreachery();
    }
    array_unshift($game[$toFaction]['treachery'], 
                            array_shift($game['treachery']['deck']));
}

function shuffleSpice() {
    global $game;
    $spiceDeckTemp = array_keys($info['spiceDeck']);
    shuffle($spiceDeckTemp);
    $spiceTempDeck1 = array_slice($spiceDeckTemp, 0, 11);
    $spiceTempDeck2 = array_slice($spiceDeckTemp, 11);
    array_merge($game['spiceDeck']['deck-1'], $spiceTempDeck1);
    array_merge($game['spiceDeck']['deck-2'], $spiceTempDeck2);
    dune_writeData('Shuffled Spice', true);
}

function dune_dealSpice($i) {
    if (($i != 1) && ($i != 2)) {
        print 'DEAL SPICE ERROR';
        return;
    }
    global $game;
    if (empty($game['spiceDeck']['deck-'.$i])) {
        dune_shuffleSpice();
    }
    array_unshift($game['spiceDeck']['discard-'.$i], 
                    array_shift($game['spiceDeck']['deck-'.$i]));
    dune_writeData();
}

function dune_checkSpice($i, $idName=false) {
    if (($i != 1) && ($i != 2)) {
        print 'CHECK SPICE ERROR';
        return;
    }
    global $game, $info;
    if (empty($game['spiceDeck']['deck-'.$i])) {
        dune_shuffleSpice();
    }
    if ($idName) {
        return $game['spiceDeck']['deck-'.$i][0];
    }
    return $info['spiceDeck'][$game['spiceDeck']['deck-'.$i][0]]['name'];
}

function dune_discardTreachery($fromFaction, $indexArray, $toDiscard = 'discard') {
    global $game;
    if (is_int($indexArray)) {
        $indexArray = array($indexArray);
    }
    rsort($indexArray);
    for ($i = 0; $i < count($indexArray); $i += 1) {
        array_unshift($game[$fromDeck][$toDiscard], $game[$fromFaction][$fromDeck][$n]);
        unset($game[$fromFaction][$fromDeck][$n]);
    }
    $game[$fromFaction][$fromDeck] = array_values($game[$fromFaction][$fromDeck]);
}

function dune_getTerritory($title, $varName, $close, $all=false) {
    global $info;
	echo
	'<form action="#" method="post"> 
    '.$title.'<select name="'.$varName.'">';
    if ($all) {
        foreach (array_keys($info['territory']) as $a) {
            echo '<option value="'.$a.'">'.$info['territory'][$a]['name'].'</option>';
        }
    }
    if (!$all) {
        foreach (array_diff(array_keys($info['territory']), array('[OFF]', '[TANKS]', '[BANK]')) as $a) {
            echo '<option value="'.$a.'">'.$info['territory'][$a]['name'].'</option>';
        }
    }
    echo '</select>';    
	if ($close) {
        echo
        '<input type="submit" value="Submit">
        </form>';
    }
}

function dune_printStatus($faction) {
    global $game, $info;
    print '<h3>Game Status:</h3>';
    // Player Order
    print '<b><u>Player Order</u>:</b>';
    foreach ($game['meta']['playerOrder'] as $x) {
        print ' '.$x.', ';
    }
    print '<br><br>';
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
    if (empty($game['treachery']['discard'])) {
        print 'None<br>';
    }
    else {
        foreach ($game['treachery']['discard'] as $y) {
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

function dune_getWaiting() {
    global $game, $info;
    print '<p><b><u>We are waiting for: </u></b><br>';
    foreach (array_keys($game['meta']['next']) as $x) {
        if ($game['meta']['next'][$x] != 'wait.php') {
            print $info['factions'][$x]['name'].'<br>';
        }
    }
}

function array_cycle($x) {
    if (!empty($x)) {
        $temp = $x[0];
        array_shift($x);
        array_push($x, $temp);
    }
    return $x;
}

function dune_moveStorm() {
    global $game, $info;
    while ($game['meta']['storm']['move'] > 0) {
        $game['meta']['storm']['move'] -= 1;
        $game['meta']['storm']['location'] += 1;
        if ($game['meta']['storm']['location'] == 19) {
            $game['meta']['storm']['location'] = 1;
        }
        if (($game['storm']['loation'] -2) % 3 == 0) {
            $game['meta']['playerOrder'] = array_cycle($game['meta']['playerOrder']);
        }
        foreach (array_keys($game['tokens']) as $y) {
            if ($info['territory'][$y]['sector'] == $game['storm']['location']) {
                foreach ($game['tokens'][$y] as $z) {
                    dune_gmMoveTokens($z, 
                                $game['tokens'][$y][$z][0],  
                                $game['tokens'][$y][$z][1],
                                $y, '[TANKS]');
                }
            }
        }
    }
}
?>
