<?php

session_start();

error_reporting(0);

include_once("http://www.gamers-live.net/analyticstracking.php");
if ($_SESSION['access'] != true) {
    header( 'Location: http://www.gamers-live.net/account/login/?msg=Please login to view this page' ) ;
    exit;
}
$mod_name = $_SESSION['channel_id'];

$user_to_ban = $_POST['username'];
$reason = $_POST['reason'];
$day = $_POST['day'];
$month = $_POST['month'];
$year = $_POST['year'];
$channel_id = $_POST['channel_id'];

// we first get data from our mysql database
$database_url = "127.0.0.1";
$database_user = "root";
$database_pw = "";

$dir_name = basename(__DIR__);

// connect to database
$connect = mysql_connect($database_url, $database_user, $database_pw) or die(mysql_error());

// select the database we need
$select_db = mysql_select_db("live", $connect) or die(mysql_error());

// first we check if user is already banned
$banned = mysql_query("SELECT * FROM chat_bans WHERE user_id='$user_to_ban' AND channel_id='$channel_id' AND banned='1'") or die(mysql_error());
$count = mysql_num_rows($banned);

if($count == 1){
    // then its already banned
    header( 'Location: http://www.gamers-live.net/account/channel/chat/ban.php?username='.$mod_name.'&channel='.$channel_id.'&msg=The user is already banned' ) ;
    exit;
}else{
    $can_ban2 = true;
}

// we now need to check if the user we want to ban is admin or mod

$get_auth = mysql_query("SELECT * FROM chat_mods WHERE user_id='$user_to_ban' AND channel_id='$channel_id'") or die(mysql_error());
$get_auth_rows = mysql_fetch_array($get_auth);

if(($get_auth_rows['moderator'] == "1") || ($get_auth_rows['admin'] == "1")){
    header( 'Location: http://www.gamers-live.net/account/channel/chat/ban.php?username='.$mod_name.'&channel='.$channel_id.'&msg=The user is MOD or ADMIN, if you wish to ban then remove the user as MOD first' ) ;
    exit;
}else{
    $can_ban1 = true;
}

if($user_to_ban == $channel_id){
    header( 'Location: http://www.gamers-live.net/account/channel/chat/ban.php?username='.$mod_name.'&channel='.$channel_id.'&msg=You cannot ban the owner of the channel' ) ;
    exit;
}else{
    $can_ban3 = true;
}

// we need to see if user is mod
$get_auth_user = mysql_query("SELECT * FROM chat_mods WHERE user_id='$mod_name' AND channel_id='$channel_id'") or die(mysql_error());
$get_auth_rows_user = mysql_fetch_array($get_auth_user);

if(($get_auth_rows_user['moderator'] == "1") || ($get_auth_rows_user['admin'] == "1")){
    $can_ban4 = true;
}

// also if we are owner
if($mod_name == $channel_id){
    $can_ban4 = true;
}


if($can_ban1 == true && $can_ban2 == true && $can_ban3 == true && $can_ban4 == true){
    // then we will ban
    $date = date("d/m-Y");
    $expire = "".$day."/".$month."/".$year."";
    $ban_user = mysql_query("INSERT INTO chat_bans (channel_id, user_id, banned_by, reason, date, banned_until, banned) VALUES ('$channel_id', '$user_to_ban', '$mod_name', '$reason', '$date', '$expire', '1')") or die(mysql_error());
    header( 'Location: http://www.gamers-live.net/account/channel/chat/ban.php?username='.$mod_name.'&channel='.$channel_id.'&msg=You banned '.$user_to_ban.' from '.$channel_id.'' ) ;
    exit;
}else{
    die('There was an error...');
}


?>