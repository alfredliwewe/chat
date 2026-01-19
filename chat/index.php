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
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Chat - <?=$_SESSION['user']['name'];?></title>
	<?php require './links2.php';?> 
	<style>
		.leftNav button{
			width: 100%;
			border-radius: 2px;
			text-align: left;
			padding: 8px 14px;
			display: block;
			background: transparent;
			border: none;
			cursor: pointer;
		}
		.leftNav button.active, .leftNav button:hover{
			background: #ede6ff;
			color: #3700b3;
		}
	</style>
</head>
<body>
	<div id="root" class="h-screen flex overflow-hidden bg-gray-100"></div>
</body>
<?php 
$index = file_get_contents("compiled/index.txt");
?>
<script type="module" src="compiled/<?=$index?>"></script>
</html>