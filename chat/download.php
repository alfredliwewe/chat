<?php 
session_start();
if (isset($_SESSION['user'], $_SESSION['user_id'])) {
	// code...
}
else{
	header("location: ../login.php");
}
require "../includes/String.php";
require_once "../functions.php";
$db = new mysql_like("../db.db");
require_once "../config.php";

if(!isset($_GET['id'])){
	echo "No id";
	exit;
}

$message = getData("messages", ['id' => $_GET['id']]);
if($message == null){
	echo "No message";
	exit;
}

if(!in_array($_SESSION['user_id'], [$message['sender'], $_SESSION['receiver']])){
	echo "No permission";
	exit;
}

if($message['attachment'] == "" || $message['attachment'] == "NULL"){
	echo "No attachment";
	exit;
}

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=" . $message['message']);
header("Content-Length: " . filesize("../uploads/" . $message['attachment']));
readfile("../uploads/" . $message['attachment']);
exit;