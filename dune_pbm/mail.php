<?php 
// Mail Commands
// Called from index.php
// uses $_SESSION['override']

// Forms ###########################################################
if (empty($_POST)){
    global $game, $info, $duneMail, $duneForum;
    if (!isset($_SESSION['sentMail'])) {
        $_SESSION['sentMail'] = false;
    }
    
    
	echo 
	'<h3>Mail</h3><br>';
    '<form action="" method="post">
    <button name="mail_action" value="inbox">Inbox</button>
    </form>
    
    <form action="" method="post">
    <button name="mail_action" value="sent">Sent Mail</button>
    </form>';
    
    if ($_SESSION['sentMail'] == false) {
        foreach ($duneMail[$_SESSION['faction']]['inbox'] as $x) {
            print $x['toFaction'].'<br>';
            print $x['fromFaction'].'<br>';
            print $x['message'].'<br>';
            print $x['time'].'<br>';
            print '<br>';
        }
    }
    if ($_SESSION['sentMail'] == true) {
        foreach ($duneMail[$_SESSION['faction']]['sent'] as $x) {
            print $x['toFaction'].'<br>';
            print $x['fromFaction'].'<br>';
            print $x['message'].'<br>';
            print $x['time'].'<br>';
            print '<br>';
        }
    }
        
    echo
    '<form action="" method="post">
    To: <select name="toFaction">
        <option value="[A]">Atreides</option>
        <option value="[B]">Bene Gesserit</option>
        <option value="[E]">Emperor</option>
        <option value="[F]">Fremen</option>
        <option value="[G]">Guild</option>
        <option value="[H]">Harkonnen</option>
            </select><br>
    <br>
    Post:<br>
    <input type="textarea" name="post">
    <input type="submit" value="Submit">
    </form> ';
}
    
// Action ########################################################
if (!empty($_POST)){
    if (isset($_POST['post'])) {
        dune_postMail($_POST['post']);
        refreshPage();
    }
}
?>
