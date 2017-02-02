<?php
//Called by index.php.
$dataPath = '/var/www/dune_pbm_data/';
$game = "";
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
    $game['stormLocation'] = mt_rand(1, 18);
    $game['stormMove'] = mt_rand(1, 6);
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
	$file = $dataPath.'dune_data'; // eclude the extension.
	if (isset($game)) {
		file_put_contents($file.'.json', json_encode($game, JSON_PRETTY_PRINT));
		file_put_contents($file.'.'.time().'.json', json_encode($game, JSON_PRETTY_PRINT));
	} else {print 'ERROR WRITING FILE';}
}

function dune_gmMove($faction, $tokens, $starTokens, $fromLoc, $toLoc) {
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
    if (empty($game['tokens'][$fromLoc])) {
        unset($game['tokens'][$fromLoc]);
    }
}

function dune_dealTerachery($toFaction) {
    global $game;
    if (empty($game['trechery']['deck'])) {
        $game['trechery']['deck'] = $game['trechery']['discard'];
        $game['trechery']['discard'] = array();
        shuffle($game['trechery']['deck']);
    }
    array_unshift($game[$faction]['trechery'], array_shift($game[$fromDeck]['deck']));
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
    do {
        array_unshift($game['spiceDeck'][$fromDeck], array_shift($game[$fromDeck]['deck']));
    
}

function dune_discard($fromDeck, $fromFaction, $indexArray, $toDiscard = 'disard') {
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
    print 'Game Status:<br><br>';
    // Show Tokens
    print '<b><u>Tokens:</b></u><br><br>';
    foreach (array_keys($game['tokens']) as $y) {
        print '<u>'.$info['territory'][$y]['name'];
        if (isset(($info['territory'][$y]['sector']))) {
            print ' (Sector '.$info['territory'][$y]['sector'].')';
        }
        print ':</u><br>';
        foreach (array_keys($game['tokens'][$y]) as $x) {
            print $info['factions'][$x]['name'].': '.$game['tokens'][$y][$x][0];
            if ($game['tokens'][$y][$x][1] != 0) {
                print '/'.$game['tokens'][$y][$x][1].'*';
            }
            print '<br>';
        }
        print '<br>';
    }
    // Treachery
    print '<b><u>Treachery:</u></b><br>';
    if (empty($game[$faction]['treachery'])) {
        print 'None';
    }
    else {
        foreach (array_keys($game[$faction]['treachery']) as $y) {
            print $info['treachry']['name'].'<br>';
        }
    }
    print '<br><br>';
    // Spice
    print '<b><u>Spice:</u></b><br>';
    print $game[$faction]['spice'].'<br><br>';
    // Notes
    print '<b><u>Notes:</u></b><br>';
    if (empty($game[$faction]['notes'])) {
        print 'None';
    }
    else {
        foreach ($game[$faction]['notes'] as $y) {
            print $y.'<br>';
        }
    }
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
