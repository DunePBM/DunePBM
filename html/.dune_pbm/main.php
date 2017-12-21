<?php
// DunePBM main.php
// Called by index.php.


//######################################################################
//###### Globals #######################################################
//######################################################################

$dataDir = '.dune_pbm_data/';
$duneForum = array();
$duneMail = array();
$debug = false;
$gmCommands = true;
$info = json_decode(file_get_contents($gameDir.'dune_info.json'), true);

//######################################################################
//###### Checks for an empty game. #####################################
//######################################################################
global $game, $gameDir;
foreach (array('dune_data', 'dune_forum', 'dune_mail') as $fileName) {
	if (!file_exists($dataDir.$fileName.'.json')) {
		$temp = json_decode(file_get_contents($gameDir.$fileName.'_start.json'), true);
		file_put_contents($dataDir.$fileName.'.json', json_encode($temp, JSON_PRETTY_PRINT));
		if ($fileName == 'dune_data') {
			dune_setupGame();
		}
	}
}
    
//######################################################################
//###### Functions #####################################################
//######################################################################

function refreshPage() {
    global $gameDir, $debug;
    if (!$debug) {
        echo '<META HTTP-EQUIV="refresh" content="0;URL="/index.php">';
        //Also Works:
        //$URL="http://yourwebsite.com/";
        //echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
    }
}

function dune_setupGame() {
    global $gameDir, $game, $info, $duneForum, $duneMail;
	$game = json_decode(file_get_contents($gameDir.'dune_data_start.json'), true);
    $duneForum = json_decode(file_get_contents($gameDir.'dune_forum_start.json'), true);
    $duneMail = json_decode(file_get_contents($gameDir.'dune_mail_start.json'), true);
    dune_writeForum();
    dune_writeMail();

    // Shuffle Player Dots
    shuffle($game['meta']['playerOrder']);
    $game['meta']['playerDots'][$game['meta']['playerOrder'][0]] = 2;
    $game['meta']['playerDots'][$game['meta']['playerOrder'][1]] = 5;
    $game['meta']['playerDots'][$game['meta']['playerOrder'][2]] = 8;
    $game['meta']['playerDots'][$game['meta']['playerOrder'][3]] = 11;
    $game['meta']['playerDots'][$game['meta']['playerOrder'][4]] = 14;
    $game['meta']['playerDots'][$game['meta']['playerOrder'][5]] = 17;
    // Treachery Card Setup
    $treacheryDeck = array_keys($info['treachery']);
    shuffle($treacheryDeck);
    $game['treachery']['deck'] = $treacheryDeck;
    // Spice Card Setup
    $game['spiceDeck']['deck-1'] = array();
    $game['spiceDeck']['deck-2'] = array();
    $spiceDeckTemp = array_keys($info['spiceDeck']);
    shuffle($spiceDeckTemp);
    $spiceDeckTemp1 = array_slice($spiceDeckTemp, 0, 10);
    $spiceDeckTemp2 = array_slice($spiceDeckTemp, 10);
    while ($info['spiceDeck'][$spiceDeckTemp1[0]]['type'] == 'worm') {
        $spiceDeckTemp1 = array_cycle($spiceDeckTemp1);
    }
    while ($info['spiceDeck'][$spiceDeckTemp2[0]]['type'] == 'worm') {
        $spiceDeckTemp2 = array_cycle($spiceDeckTemp2);
    }
    $spiceDeckTemp = array_keys($info['spiceDeck']);
    shuffle($spiceDeckTemp);
    $spiceDeckTemp1 = array_merge($spiceDeckTemp1, array_slice($spiceDeckTemp, 0, 11));
    $spiceDeckTemp2 = array_merge($spiceDeckTemp2, array_slice($spiceDeckTemp, 11));
    $game['spiceDeck']['deck-1'] = $spiceDeckTemp1;
    $game['spiceDeck']['deck-2'] = $spiceDeckTemp2;
    // Traitor Setup
    $traitorDeck = array_keys($info['leaders']);
    shuffle($traitorDeck);
    $game['traitorDeck']['deck'] = $traitorDeck;
    foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
        while ($game['traitorDeck']['deck'][0][1] == $faction[1]) {
			$game['traitorDeck']['deck'] = array_cycle($game['traitorDeck']['deck']);
		}
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

function dune_readData($fm = true, $fileTimestamp = '') {
	global $gameDir, $dataDir, $game, $duneForum, $duneMail;
	$file = $dataDir.'dune_data'; // eclude the extension.
	if ($fileTimestamp != '') {
		$file .= '.'.$fileTimestamp;
	}
	$gameTemp = json_decode(file_get_contents($file.'.json'), true);
    if ($fm) {
        dune_readForum();
        dune_readMail();
    }
    if (isset($gameTemp)) {
		$game = $gameTemp;
	} else {
		print '<script>alert(\'ERROR REDING FILE\');</script>';
	} 
}

function dune_readForum($fileTimestamp = '') {
	global $gameDir, $dataDir, $game, $duneForum, $duneMail;
	$file = $dataDir.'dune_forum'; // eclude the extension.
    if ($fileTimestamp != '') {
		$file .= '.'.$fileTimestamp;
	}
    $duneForum = json_decode(file_get_contents($file.'.json'), true);
    if (!isset($duneForum)) {
		print '<script>alert(\'ERROR REDING FORUM\');</script>';
	}
}

function dune_readMail($fileTimestamp = '') {
	global $gameDir, $dataDir, $game, $duneForum, $duneMail;
    $file = $dataDir.'dune_mail'; // eclude the extension.
    if ($fileTimestamp != '') {
		$file .= '.'.$fileTimestamp;
	}
    $duneMail = json_decode(file_get_contents($file.'.json'), true);
    if (!isset($duneMail)) {
		print '<script>alert(\'ERROR REDING MAIL\');</script>';
	}
}

function dune_writeData($event='', $gm=false) {
	global $gameDir, $dataDir, $game, $duneForum, $duneMail;
	$file = $dataDir.'dune_data'; // eclude the extension.
	
	if (isset($game)) {
		// Timestamps
		$newTimestamp = time();
		$oldTimestamp = $game['meta']['timestamps']['dataCurrent'];
		$game['meta']['timestamps']['dataCurrent'] = $newTimestamp;
		$game['meta']['timestamps']['dataUndo'] = $oldTimestamp;
		$game['history'][] = $newTimestamp.': '.$event;
		
        // Write new move.
        $game['meta']['eventNumber'] += 1;
        if ($event != '') {
            $game['meta']['event'] = $event;
        }
        if (isset($_SESSION['faction'])) {
			$game['meta']['faction'] = $_SESSION['faction'];
		} else {
			$game['meta']['faction'] = '[DUNE]';
		}
        if ($gm) {
            $game['meta']['faction'] = '[DUNE]';
        }
        file_put_contents($file.'.json', json_encode($game, JSON_PRETTY_PRINT));
		file_put_contents($file.'.'.time().'.json', json_encode($game, JSON_PRETTY_PRINT));
	} else {
		print '<script>alert(\'ERROR WRITING FILE\');</script>';
	}
}

function dune_writeForum() {
	global $gameDir, $dataDir, $game, $duneForum, $duneMail;
    $file = $dataDir.'dune_forum'; // eclude the extension.
    file_put_contents($file.'.json', json_encode($duneForum, JSON_PRETTY_PRINT));
    file_put_contents($file.'.'.time().'.json', json_encode($duneForum, JSON_PRETTY_PRINT));
}

function dune_writeMail() {
	global $gameDir, $dataDir, $game, $duneForum, $duneMail;
    $file = $dataDir.'dune_mail'; // eclude the extension.
    file_put_contents($file.'.json', json_encode($duneMail, JSON_PRETTY_PRINT));
    file_put_contents($file.'.'.time().'.json', json_encode($duneMail, JSON_PRETTY_PRINT));
}

function dune_postForum($message, $gm = false) {
    global $gameDir, $game, $info, $duneForum, $duneMail;
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
    global $gameDir, $game, $info, $duneForum, $duneMail;
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

function dune_undoMove($forceUndo = false) {
	global $gameDir, $dataDir, $game;
    $file = $dataDir.'dune_data'; // eclude the extension.
    dune_readData();
    if (($game['meta']['faction'] == $_SESSION['faction']) || $forceUndo) {
		dune_postForum('Game Move Undone: '.$game['meta']['event'], true);
		dune_readData(true, $game['meta']['timestamps']['dataUndo']);
		$game['meta']['timestamps']['dataCurrent'] = $game['meta']['timestamps']['dataUndo'];
		dune_writeData('Game Move Undone: '.$game['meta']['event']);
    }
    else {
		print '<script>alert("Move can not be undone.");</script>';
	}
    dune_readData();
}

function dune_gmMoveTokens($faction, $tokens, $starTokens, $fromLoc, $toLoc, $coexisting=false) {
	global $gameDir, $game;
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
    global $gameDir, $game;
    $game['treachery']['deck'] = array_slice($game['treachery']['discard'], 2);
    $game['treachery']['discard'] = array_slice($game['treachery']['discard'], 0, 2);
    shuffle($game['treachery']['deck']);
    dune_writeData('Shuffled Treachery', true);
}
    
function dune_dealTreachery($toFaction) {
    global $gameDir, $game;
    if (empty($game['treachery']['deck'])) {
        dune_shuffleTreachery();
    }
    array_unshift($game[$toFaction]['treachery'], 
                            array_shift($game['treachery']['deck']));
}

function shuffleSpice() {
    global $gameDir, $game;
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
    global $gameDir, $game;
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
    global $gameDir, $game, $info;
    if (empty($game['spiceDeck']['deck-'.$i])) {
        dune_shuffleSpice();
    }
    if ($idName) {
        return $game['spiceDeck']['deck-'.$i][0];
    }
    return $info['spiceDeck'][$game['spiceDeck']['deck-'.$i][0]]['name'];
}

function dune_checkRoundEnd($oldMarker, $newPhp, $message, $subLocation = false) {
    global $gameDir, $game;
    $roundOver = true;
    dune_readData();
    if ($subLocation) {
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			if ($game[$oldMarker]['next'][$faction] != 'wait') {
				$roundOver = false;
			}
        }
    } else {
		foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
			if ($game['meta']['next'][$faction] != 'wait.php') {
				$roundOver = false;
			}
        }
    }
    if ($roundOver == true) {
        unset($game[$oldMarker]);
        foreach (array('[A]', '[B]', '[E]', '[F]', '[G]', '[H]') as $faction) {
            $game['meta']['next'][$faction] = $newPhp;
        }
        dune_writeData($message, true);
        dune_postForum($message, true);
    }
    return;
}   

function dune_discardTreachery($faction, $cardName) {
    global $gameDir, $game;
    $key = NULL;
    if (is_int($cardName)) {
        $key = $cardName;
    }
    if (is_string($cardName)) {
        $key = array_search($cardName, $game[$faction]['treachery']);
    }
    if (isset($key)) {
        array_unshift($game['treacheryDeck']['discard'], $game[$faction]['treachery'][$key]);
        unset($game[$faction]['treachery'][$key]);
        $game[$faction]['treachery'] = array_values($game[$faction]['treachery']);
    }
}


function dune_getTerritory($title, $varName, $close, $all=false) {
    global $gameDir, $info;
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

function dune_getWaiting() {
    global $gameDir, $game, $info;
    print '<p><b><u>We are waiting for: </u></b><br>';
    foreach (array_keys($game['meta']['next']) as $x) {
        if ($game['meta']['next'][$x] != 'wait') {
            print $info['factions'][$x]['name'].'<br>';
        }
    }
}

function gameAlert($m) {
	print '<script>alert(\''.$m.'\');</script>';
}

function array_cycle($x, $forward = true) {
    if (!empty($x)) {
        if ($forward) {
            array_push($x, array_shift($x));
        } else {
            array_unshift($x, array_pop($x));
        }
    }
    return $x;
}

?>
