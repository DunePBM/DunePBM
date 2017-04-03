<?php 
// Forum Commands
// Called from index.php
// uses $_SESSION['override']

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info, $duneMail, $duneForum;
	echo 
	'<h3>Forum</h3><br>';
    foreach ($duneForum as $x) {
        print $x['faction'].'<br>';
        print $x['message'].'<br>';
        print $x['time'].'<br>';
        print '<br>';
    }
        
    echo
    '<form action="" method="post">
    Post:<br>
    <input type="textarea" name="post">
    <input type="submit" value="Submit">
    </form> ';
}
    
// Action ########################################################
if (!empty($_POST)){
    if (isset($_POST['post'])) {
        dune_postForum($_POST['post']);
        refreshPage();
    }
}
?>
