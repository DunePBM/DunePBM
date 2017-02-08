<?php
//Called by index.php.
$dataPath = '/var/www/dune_pbm_data/';
$game = "";
$debug = false;
$info = json_decode(file_get_contents($gamePath.'dune_info.json'), true);

function dune_setupGame() {
    global $dataPath, $gamePath, $game, $info;
	$game = json_decode(file_get_contents($gamePath.'dune_data_start.json'), true);
    // Treachery Card Setup
    $treacheryDeck = array_keys($info['treachery']);
    shuffle($treacheryDeck);
    $game['treachery']['deck'] = $treacheryDeck;
    // Spice Card Setup
    $spiceDeck1 = array_keys($info['spice_deck']);
    $spiceDeck2 = array_keys($info['spice_deck']);
    shuffle($spiceDeck1);
    shuffle($spiceDeck2);
    $game['spiceDeck']['deck'] = array_merge($spiceDeck1, $spiceDeck2);
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
    $game['storm']['location'] = mt_rand(1, 18);
    $game['storm']['move'] = mt_rand(1, 6);
    dune_writeData();
}

function dune_readData() {
	global $dataPath, $game;
	$file = $dataPath.'dune_data'; // eclude the extension.
	$game = json_decode(file_get_contents($file.'.json'), true);
	if (!isset($game)) {print 'ERROR REDING FILE';}
}

function dune_writeData() {
	global $dataPath, $game;
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
        file_put_contents($file.'.json', json_encode($game, JSON_PRETTY_PRINT));
		file_put_contents($file.'.'.time().'.json', json_encode($game, JSON_PRETTY_PRINT));
	} else {print 'ERROR WRITING FILE';}
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

function dune_gmMove($faction, $tokens, $starTokens, $fromLoc, $toLoc, $coexisting=false) {
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

function dune_dealTreachery($toFaction) {
    global $game;
    if (empty($game['treachery']['deck'])) {
        $game['treachery']['deck'] = $game['treachery']['discard'];
        $game['treachery']['discard'] = array();
        shuffle($game['treachery']['deck']);
    }
    array_unshift($game[$toFaction]['treachery'], array_shift($game['treachery']['deck']));
}

function dune_dealSpice($toDiscard) {
    global $game;
    if (empty($game['spice_deck']['deck'])) {
        $game['spice_deck']['deck'] = $game['$fromDeck']['discard-1'];
        $game['spice_deck']['deck'] .= $game['$fromDeck']['discard-2'];
        $game['spice_deck']['discard-1'] = array();
        $game['spice_deck']['discard-2'] = array();
        shuffle($game['spice_deck']['deck']);
    }
    array_unshift($game['spiceDeck'][$fromDeck], array_shift($game[$fromDeck]['deck']));
}

function dune_discard($fromDeck, $fromFaction, $indexArray, $toDiscard = 'discard') {
    global $game;
    if (is_int($indexArray)) {
        $indexSArray = array($indexArray);
    }
    rsort($indexArray);
    for ($i = 0; $i < count($indexArray); $i += 1) {
        array_unshift($game[$fromDeck][$toDiscard], $game[$fromFaction][$fromDeck][$n]);
        unset($game[$fromFaction][$fromDeck][$n]);
    }
    $game[$fromFaction][$fromDeck] = array_values($game[$fromFaction][$fromDeck]);
}

function dune_getTerritory($title, $varName, $close) {
    global $info;
	echo
	'<form action="#" method="post"> 
    '.$title.'<select name="'.$varName.'">';
    foreach (array_diff(array_keys($info['territory']), array('[OFF]', '[TANKS]')) as $a) {
        echo '<option value="'.$a.'">'.$info['territory'][$a]['name'].'</option>';
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
    print '<br><h3>'.$info['factions'][$faction]['name'].' Game Status:</h3><br>';
    // The Storm
    print '<b><u>Storm</u>:</b> ';
    print 'The storm is in Sector '.$game['storm']['location'].'.<br><br>';
    // Spice Treasury
    print '<b><u>Spice Treasury</u>:</b> ';
    print $game[$faction]['spice'].' spice.<br><br>';
    // Show Tokens & Spice
    print '<b><u>Token & Spice Locations</u>:</b><br><br>';
    foreach (array_keys($game['tokens']) as $y) {
        print '<u>'.explode(' (', $info['territory'][$y]['name'])[0];
        if (isset($info['territory'][$y]['sector'])) {
            print ' (Sector '.$info['territory'][$y]['sector'].')';
        }
        print ':</u><br>';
        foreach (array_keys($game['tokens'][$y]) as $x) {
            if ($x == 'Spice') {
                print $info['factions'][$x]['name'].': '.$game['tokens'][$y][$x];
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
        print '<br>';
    }
    // Treachery
    print '<b><u>Treachery</u>:</b><br>';
    if (empty($game[$faction]['treachery'])) {
        print 'None';
    }
    else {
        foreach ($game[$faction]['treachery'] as $y) {
            print $info['treachery'][$y]['name'].'<br>';
        }
    }
    print '<br><br>';
    // Notes
    print '<b><u>Notes</u>:</b><br>';
    if (empty($game[$faction]['notes'])) {
        print 'None';
    }
    else {
        foreach ($game[$faction]['notes'] as $y) {
            print $y.'<br>';
        }
    }
}

function dune_moveStorm($num) {
}
    

/*Factions & Locations:
    [A]tredues,
    [B]ene Gesserit
    [E]mperor
    [F]remen
    [G]uild
    [H]arkonnen
    [T]anks
    [O]ff World
    [D]une Board Game (Hidden Game Info)

dune_pbm newgame

dune_pbm run filename

dune_pbm setup-traitor faction traitor

dune_pbm setup-tokens f st st* fws fws* fwe fwe* traitor
dune_pbm setup-tokens b predictionFaction predicitonTurn startLoc traitor

dune_pbm move-storm

dune_pbm deal-spice

dune_pbm bid faction card# spice#
dune_pbm overtime-bid faction card# spice#
dune_pbm end-auction

dune_pbm revive faction tokens starTokens
dune_pbm ship faction tokens starTokens toLocation
dune_pbm move faction tokens starTokens fromLocation toLococation

dune_pbm battle-location aFaction dFaction location
dune_pbm battle-plan faction leader attack defense dial support
dune_pbm battle

dune_pbm show faction card


dune_pbm gm-deal faction
dune_pbm gm-hidden-deal faction
dune_pbm gm-discard faction card
*/
?>
