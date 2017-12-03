<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$gameDir = '.dune_pbm/';
include_once $gameDir.'main.php';
?>

<head>
<title>Dune PBM</title>
<meta http-equiv="description" content="page description" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<style type="text/css">@import "styles.css";</style>
</head>
<body>

<h1>Dune PBM</h1>

<?php
global $gameDir, $game, $game, $info, $duneForum, $duneMail;
if (!isset($_SESSION['faction'])) {
    dune_readData();
    include $gameDir.'login.php';
    print '<hr>';
    if (!isset($_SESSION['faction'])) {
        dune_getWaiting();
    }
}
if (isset($_SESSION['override']) && isset($_SESSION['faction'])) {
    dune_readData();
    include $gameDir.'header.php';
    if (isset($_SESSION['override'])) { //This is a bug-fix
        include $gameDir.$_SESSION['override'];
    }
} 
if (!isset($_SESSION['override']) && isset($_SESSION['faction'])) {
    dune_readData();
    include $gameDir.'header.php';
    if ($game['meta']['next'] != 'wait') {
		include $gameDir.$game['meta']['round'];
	} else {
		include $gameDir.'wait.php';
	}
}
?>

</body>
</html>
