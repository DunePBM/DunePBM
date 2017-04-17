<?php 
// Forum Commands
// Called from index.php
// uses $_SESSION['override']

if (empty($_POST)){
    global $game, $info;
    
    //##############################################################
    //## First Run #################################################
    //##############################################################
    if (!isset($game['movementRound'])) {
        $game['movementRound'] = array();
    }

//######################################################################
//###### Forms #########################################################
//######################################################################
if (empty($_POST)){
    global $game, $info, $duneMail, $duneForum;
    
    //##############################################################
    //## First Run #################################################
    //##############################################################
    if (!isset($game['forum'])) {
        $game['forum'] = array();
        $game['forum']['page'] = 1;
    }

    //##############################################################
    //## Every Run #################################################
    //##############################################################
	echo 
	'<h3>Forum</h3><br>';
    //## Select Page ###############################################
    
    
    //## Print Forum ###############################################
    foreach ($duneForum as $x) {
        print $x['faction'].'<br>';
        print $x['message'].'<br>';
        print $x['time'].'<br>';
        print '<br>';
    }
    
    //## Post To Forum #############################################
    echo
    '<form action="" method="post">
    Post:<br>
    <input type="textarea" name="post">
    <input type="submit" value="Submit">
    </form> ';
}
    
//######################################################################
//###### Post ##########################################################
//######################################################################
if (!empty($_POST)){
    if (isset($_POST['post'])) {
        dune_postForum($_POST['post']);
        refreshPage();
    }
}

//######################################################################
//###### Actions #######################################################
//######################################################################



?>
