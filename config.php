<?php
$config = [];
$read = $db->query("SELECT * FROM config");
while ($row = $read->fetch_assoc()) {
    $config[$row['name']] = $row['value'];
}

$img_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];