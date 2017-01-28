<?php
//Called by index.php.
$dataPath = '/var/www/dune_pbm_data/';
$game = "";
$info = json_decode(file_get_contents($gamePath.'dune_info.json'), true);

function dune_setupGame() {
    global $dataPath, $gamePath, $game, $info;
	$game = json_decode(file_get_contents($gamePath.'dune_data_start.json'), true);
    //Treachery Card Setup
    $treacheryDeck = array_keys($info['treachery']);
    shuffle($treacheryDeck);
    $game['treacheryDeck']['deck'] = $treacheryDeck;
    //Spice Card Setup
    $spiceDeck1 = array_keys($info['spice_deck']);
    $spiceDeck2 = array_keys($info['spice_deck']);
    shuffle($spiceDeck1);
    shuffle($spiceDeck2);
    $game['spiceDeck']['deck'] = array_merge($spiceDeck1, $spiceDeck2);
    //Traitor Setup
    $traitorDeck = array_keys($info['leaders']);
    shuffle($traitorDeck);
    $game['traitorDeck']['deck'] = $traitorDeck;
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
        for ($i = 0; $i <4; $i++) {
            dune_deal('traitorDeck', $faction);
        }
    }
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
	if ($game['tokens'][$fromLoc][$faction] == [0,0]) {
        unset($game['tokens'][$fromLoc][$faction]);
    }
    if (empty($game['tokens'][$fromLoc])) {
        unset($game['tokens'][$fromLoc]);
    }
	if (empty($game['tokens'][$fromLoc])) {
        unset($game['tokens'][$fromLoc]);
    }
}

function dune_deal($fromDeck, $toFaction) {
    global $game;
    if (empty($game[$fromDeck]['deck'])) {
        $game[$fromDeck]['deck'] = $game['$fromDeck']['discard'];
        $game[$fromDeck]['discard'] = array();
        shuffle($game[$fromDeck]['deck']);
    }
    array_unshift($game[$fromDeck][$toFaction], array_shift($game[$fromDeck]['deck']));
}

function dune_discard($fromDeck, $fromFaction, $indexArray, $toDiscard = 'disard') {
    global $game;
    if (is_int($indexArray)) {
        $indexSArray = array($indexArray);
    }
    rsort($indexArray);
    for ($i = 0; $i < count($indexArray); $i += 1) {
        array_unshift($game[$fromDeck][$toDiscard], $game[$fromDeck][$fromFaction][$n]);
        unset($game[$fromDeck][$fromFaction][$n]);
        $game[$fromDeck][$fromFaction] = array_values($game[$fromDeck][$fromFaction]);
    }
}

function getTerritory($title, $varName, $close) {
    global $info;
	echo
	'<form action="#" method="post"> 
    '.$title.'<select name="'.$varName.'">';
    foreach ($info['territory'] as $a) {
        echo '<option value="'.$a['name'].'</option>';
    }
    echo '</select>';
	if ($close) {
        echo
        '<input type="submit" value="Submit">
        </form>';
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
