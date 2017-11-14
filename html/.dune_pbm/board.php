<?php 
// Dune Board
// Called from setup-tokens.php
// uses $_SESSION['override']

global $data, $info, $gameDir;
dune_readData();


echo file_get_contents($gameDir.'DunePBM_Board.svg');
?>
