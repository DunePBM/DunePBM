<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$gamePath = '/var/www/dune_pbm/';
include_once $gamePath.'main.php';
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
global $gamePath;
if (!isset($_SESSION['faction'])) {
    dune_readData();
    include $gamePath.'login.php';
    dune_getWaiting();
} 
if (isset($_SESSION['override']) && isset($_SESSION['faction'])) {
    dune_readData();
    include $gamePath.'header.php';
    if (isset($_SESSION['override'])) { //This is a bug-fix
        include $gamePath.$_SESSION['override'];
    }
} 
if (!isset($_SESSION['override']) && isset($_SESSION['faction'])) {
    dune_readData();
    include $gamePath.'header.php';
    include $gamePath.$game['meta']['next'][$_SESSION['faction']];
}
?>

</body>
</html>
