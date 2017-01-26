<?php
//Called by index.php.
$dataPath = '/var/www/dune_pbm_data/';
$game = "";

function dune_setupGame() {
    global $dataPath, $gamePath, $game;
	$game = json_decode(file_get_contents($gamePath.'dune_data_start.json'), true);
	$tempFile = $gamePath.'dune_info.json';
    $tempData = json_decode(file_get_contents($tempFile), true);
    $leaderDeck = array_keys($tempData['leaders']);
    shuffle($leaderDeck);
    $game['leaderDeck'] = $leaderDeck;
    $spiceDeck = array_keys($tempData['spice_deck']);
    shuffle($spiceDeck);
    $game['spiceDeck'] = $spiceDeck;
    shuffle($spiceDeck);
    $game['spiceDeck'] = $spiceDeck;
    $treacheryDeck = array_keys($tempData['treachery']);
    shuffle($treacheryDeck);
    $game['treacheryDeck'] = $treacheryDeck;
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

function getTerritory($title, $varName, $close) {
	echo
	'<form action="#" method="post"> 
    '.$title.'<select name="'.$varName.'">
        <option value="[ARR]">Arrakeen</option>
        <option value="[ARS-1]">Arsunt &lt11&gt</option>
        <option value="[ARS-2]">Arsunt &lt12&gt</option>
        <option value="[BAS]">Basin</option>
        <option value="[BOTC]">Blight of the Cliff &lt14&gt</option>
        <option value="[BOTC]">Blight of the Cliff &lt15&gt</option>
        <option value="[BL-1]">Broken Land &lt11&gt</option>
        <option value="[BL-2]">Broken Land &lt12&gt</option>
        <option value="[CAR]">Carthag</option>
        <option value="[CD-1]">Cielago Depression &lt1&gt</option>
        <option value="[CD-2]">Cielago Depression &lt2&gt</option>
        <option value="[CD-3]">Cielago Depression &lt3&gt</option>
        <option value="[CE-1]">Cielago East &lt2&gt</option>
        <option value="[CE-2]">Cielago East &lt3&gt</option>
        <option value="[CN-1]">Cielago North &lt1&gt</option>
        <option value="[CN-2]">Cielago North &lt2&gt</option>
        <option value="[CN-3]">Cielago North &lt3&gt</option>
        <option value="[CS-1]">Cielago South &lt2&gt</option>
        <option value="[CS-2]">Cielago South &lt3&gt</option>
        <option value="[FP]">Funeral Plain</option>
        <option value="[FWE-1]">False Wall East &lt4&gt</option>
        <option value="[FWE-2]">False Wall East &lt5&gt</option>
        <option value="[FWE-3]">False Wall East &lt6&gt</option>
        <option value="[FWE-4]">False Wall East &lt7&gt</option>
        <option value="[FWE-5]">False Wall East &lt8&gt</option>
        <option value="[FWS-1]">False Wall South &lt4&gt</option>
        <option value="[FWS-2]">False Wall South &lt5&gt</option>
        <option value="[FWW-1]">False Wall West &lt16&gt</option>
        <option value="[FWW-2]">False Wall West &lt17&gt</option>
        <option value="[FWW-3]">False Wall West &lt18&gt</option>
        <option value="[GK]">Gara Kulon</option>
        <option value="[HB-1]">Hagga Basin &lt12&gt</option>
        <option value="[HB-2]">Hagga Basin &lt13&gt</option>
        <option value="[HE-1]">Habbanya Erg &lt16&gt</option>
        <option value="[HE-2]">Habbanya Erg &lt17&gt</option>
        <option value="[HITR]">Hole in the Rock</option>
        <option value="[HP-1]">Cielago East &lt2&gt</option>
        <option value="[HP-2]">Cielago East &lt3&gt</option>
        <option value="[HRF-1]">Habbanya Ridge Flat &lt17&gt</option>
        <option value="[HRF-2]">Habbanya Ridge Flat &lt18&gt</option>
        <option value="[HRS]">Habbanya Ridge Sietch</option>
        <option value="[IB-1]">Imperial Basin &lt9&gt</option>
        <option value="[IB-2]">Imperial Basin &lt10&gt</option>
        <option value="[IB-3]">Imperial Basin &lt11&gt</option>
        <option value="[MER-1]">Meridian &lt1&gt</option>
        <option value="[MER-2]">Meridian &lt2&gt</option>        
        <option value="[OG-1]">Old Gap &lt9&gt</option>
        <option value="[OG-2]">Old Gap &lt10&gt</option>
        <option value="[OG-3]">Old Gap &lt11&gt</option>
        <option value="[PB-1]">Plastic Basin &lt12&gt</option> 12},
        <option value="[PB-2]">Plastic Basin &lt13&gt</option> 13},
        <option value="[PB-3]">Plastic Basin &lt14&gt</option> 14},
        <option value="[PM-1]">Pasty Mesa &lt5&gt</option> 5}, 
        <option value="[PM-2]">Pasty Mesa &lt6&gt</option> 6}, 
        <option value="[PM-3]">Pasty Mesa &lt7&gt</option> 7}, 
        <option value="[PM-4]">Pasty Mesa &lt8&gt</option> 8}, 
        <option value="[PS]">Polar Sink</option>
        <option value="[RC]">Red Chasm</option>
        <option value="[RO-1]">Rock Outcroppings &lt13&gt</option>
        <option value="[RO-2]">Rock Outcroppings &lt14&gt</option>
        <option value="[RWW]">Rim Wall West</option>
        <option value="[SM-1]">South Mesa &lt4&gt</option>
        <option value="[SM-2]">South Mesa &lt5&gt</option>
        <option value="[SM-3]">South Mesa &lt6&gt</option>
        <option value="[SM-4]">South Mesa &lt7&gt</option>
        <option value="[SR]">Shaya Ridge</option>
        <option value="[ST]">Seitch Tabr</option>
        <option value="[SW-1]">Shield Wall &lt8&gt</option>
        <option value="[SW-2]">Shield Wall &lt9&gt</option>
        <option value="[TGERF]">The Greater Flat</option>
        <option value="[TGF]">The Great Flat</option>
        <option value="[TME-1]">The Minor Erg &lt5&gt</option>
        <option value="[TME-2]">The Minor Erg &lt6&gt</option>
        <option value="[TME-3]">The Minor Erg &lt7&gt</option>
        <option value="[TME-4]">The Minor Erg &lt8&gt</option>
        <option value="[TSI-1]">Tsimpo &lt11&gt</option>
        <option value="[TSI-2]">Tsimpo &lt12&gt</option>
        <option value="[TSI-3]">Tsimpo &lt13&gt</option>
        <option value="[TS]">Tuek\'s Sietch</option>
        <option value="[WPN-1]">Wind Pass North &lt17&gt</option>
        <option value="[WPN-2]">Wind Pass North &lt18&gt</option>
        <option value="[WP-1]">Wind Pass &lt14&gt</option>
        <option value="[WP-2]">Wind Pass &lt15&gt</option>
        <option value="[WP-3]">Wind Pass &lt16&gt</option>
        <option value="[WP-4]">Wind Pass &lt17&gt</option>
        <option value="[OFF]">Off Planet</option>
        <option value="[TANKS]">Tanks</option>
    </select>';
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
