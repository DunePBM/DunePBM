<?php 
// Dune Board
// Called from setup-tokens.php
// uses $_SESSION['override']

global $data, $info, $gamePath;
dune_readData();

echo file_get_contents($gamePath.'DunePBM_Board.svg');
?>
