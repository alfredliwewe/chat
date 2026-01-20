<?php 
session_start();
if (!isset($_SESSION['user'], $_SESSION['user_id'])) {
echo json_encode([
	"status" => "error",
	"message" => "Unauthorized"
]);
exit;
}
require "../../includes/String.php";
require_once "../../functions.php";
require_once "../../custom_functions.php";
$db = new mysql_like("../../db.db");
require_once "../../config.php";

$person_id = $user_id = $_SESSION['user_id'];
$time = time();

if(isset($_GET['getUser'])){
    $data = getData("users", ['id' => $user_id]);

    JSON::dump($data);
}
elseif (isset($_FILES['change_picture'])) {
    $filename = $_FILES['change_picture']['name'];
    $filename = Crypto::random_filename().".".pathinfo($filename, PATHINFO_EXTENSION);
    move_uploaded_file($_FILES['change_picture']['tmp_name'], "../../uploads/".$filename);

    db_update("users", ['picture' => $filename], ['id' => $user_id]);

    echo json_encode([
        'status' => true, 
        'message' => "Success",
        'data' => getData("users", ['id' => $user_id]),
        'filename' => $filename,
        'picture' => $filename
    ]);
}
elseif(isset($_POST['updateUser'], $_POST['data'])){
    $data = json_decode($_POST['data'], true);

    //check if there are any changes at all
    if($data['name'] == $_SESSION['user']['name'] && $data['phone'] == $_SESSION['user']['phone'] && $data['email'] == $_SESSION['user']['email']){
        echo json_encode([
            'status' => false, 
            'message' => "No changes made"
        ]);
        exit;
    }

    db_update("users", [
        'name' => $data['name'],
        'phone' => $data['phone'],
        'email' => $data['email'],
    ], ['id' => $user_id]);
    $data = getData("users", ['id' => $user_id]);
    $_SESSION['user'] = $data;

    echo json_encode([
        'status' => true, 
        'message' => "Success",
        'data' => $data
    ]);
}
elseif(isset($_GET['getUsers'])){
    JSON::dump(getAll("users", [
        'status' => 'active',
        'id !=' => $user_id
    ]));
}
elseif(isset($_POST['sendMessage'], $_POST['friend_id'], $_POST['message'])){
    $message_id = db_insert("messages", [
        'sender' => $person_id,
        'receiver' => $_POST['friend_id'],
        'message' => $_POST['message'],
        'type' => 'text',
        'time' => $time,
        'status' => 'unread',
        'read_time' => 0,
        'attachment' => ''
    ]);

    update_last_message($person_id, $_POST['friend_id'], $message_id);

    echo json_encode(['status' => true, 'message' => "Success"]);
}
elseif(isset($_FILES['file_attachment'], $_POST['friend_id'])){
    $filename = $_FILES['file_attachment']['name'];
    $original_filename = $filename;
    $filename = Crypto::random_filename().".".pathinfo($filename, PATHINFO_EXTENSION);
    move_uploaded_file($_FILES['file_attachment']['tmp_name'], "../../uploads/".$filename);
    $message_id = db_insert("messages", [
        'sender' => $person_id,
        'receiver' => $_POST['friend_id'],
        'message' => $original_filename,
        'type' => 'file',
        'time' => $time,
        'status' => 'unread',
        'read_time' => 0,
        'attachment' => $filename
    ]);

    update_last_message($person_id, $_POST['friend_id'], $message_id);

    echo json_encode(['status' => true, 'message' => "Success"]);
}

elseif (isset($_GET['getChatHeads'])) {
    $rows = [];

    $chat_friends = get_chat_friends($person_id);
    // JSON::dump($chat_friends);
    // exit;

    $messages = getAll("messages", "id", [
        'id' => array_column($chat_friends, 'message')
    ]);

    $default_person = default_person();

    $people = getAll("users", "id", [
        'id' => array_column($chat_friends, 'user')
    ]);

    foreach ($chat_friends as $friend_row) {
        if(!isset($messages[$friend_row['message']])) continue;
        $row = $messages[$friend_row['message']];
    
        $friend_id = $friend_row['user'];

        $row['user_data'] = $people[$friend_id] ?? $default_person;

        $row['ago'] = time_ago($row['time']);
        $row['sender_name'] = $row['sender'] == $person_id ? "me" : $row['user_data']['name'];
        $row['chat_type'] = "friend";
        $row['message'] = strip_tags($row['message']);
        //max 6 words
        $row['message'] = Strings::words($row['message'], 6);
        $row['unreads'] = db_get_count("messages", "id", [
            'receiver' => $person_id,
            'sender' => $friend_id,
            'status' => ['sent','unread']
        ]);
        array_push($rows, $row);
    }

    $groups = getGroups($person_id);
    if(count($groups) > 0){
        foreach($groups as $group){
            $row = $db->query("SELECT * FROM group_messages WHERE `group` = '$group' ORDER BY id DESC LIMIT 1")->fetch_assoc();
            if($row != null){
                //$row = Arrays::merge($row, getData("group_messages", ['id' => $row['id']]));
                $row['chat_type'] = "group";
                $row['user_data'] = getData("students", ['id' => $row['sender']]);
                $row['group_data'] = getData("groups", ['id' => $row['group']]);
                $row['ago'] = time_ago($row['time']);
                if ($row['type'] == "notification") {
                    $row['message'] = replace_pattern($row['message'], []);
                }
                $last_id = getData("group_reads", [
                    'group_id' => $group,
                    'person' => $person_id
                ])['message'] ?? 0;
                $row['unreads'] = db_get_count("group_messages", "id", [
                    'id >' => $last_id,
                    'group' => $group
                ]);
                array_push($rows, $row);
            }
        }
    }

    //sort by time
    usort($rows, function($a, $b) {
        return $b['time'] - $a['time'];
    });

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows);
}

elseif (isset($_GET['getMessages'])) {
    $friend_id = $_GET['getMessages'];
    $user_id = $person_id;
    $friend_type = $_GET['friend_type'] ?? "student";

    $people_data = getAll("users", "id", [
        'id' => [$user_id, $friend_id]
    ]);

    $user_data = $people_data[$user_id];
    $friend_data = $people_data[$friend_id];

    $rows = [];
    $to_mark_read = [];

    $read = $db->query("SELECT * FROM messages WHERE (sender = '$user_id' AND receiver = '$friend_id') OR (sender = '$friend_id' AND receiver = '$user_id')");
    while ($row = $read->fetch_assoc()) {
        $row['user_data'] = $user_id == $row['sender'] ? $user_data : $friend_data;
        $row['ago'] = time_ago($row['time']);
        $row['sender_type'] = $user_id == $row['sender'] ? "me" : "friend";
        $is_you_the_sender = $user_id == $row['sender'];
        if(!$is_you_the_sender){
            array_push($to_mark_read, $row['id']);
        }
        array_push($rows, $row);
    }


    if (!empty($to_mark_read)) {
        db_update("messages", ['status' => 'read'], ['id' => $to_mark_read]);
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows);
}
elseif (isset($_GET['getGroupMessages'])) {
    $group_id = $_GET['getGroupMessages'];
    $user_id = $person_id;

    $rows = getAll("group_messages", ['group' => $group_id]);

    foreach ($rows as &$row) {
        $row['user_data'] = getData("students", ['id' => $row['sender']]);
        $row['ago'] = time_ago($row['time']);
        $row['sender_name'] = $row['sender'] == $user_id ? "You" : $row['user_data']['name'];
        $row['chat_type'] = "group";
        $row['sender_type'] = $row['sender'] == $user_id ? "user" : "friend";
        if ($row['type'] == "notification") {
            $row['message'] = replace_pattern($row['message'], []);
        }
    }

    if (count($rows) > 0) {
        $last = end($rows);
        // update last seen message...
        $check = getData("group_reads", [
            'group_id' => $group_id,
            'person' => $person_id,
        ]);
        if ($check != null) {
            if ($check['message'] != $last['id']) {
                db_update("group_reads", [
                    'message' => $last['id']
                ], ['id' => $check['id']]);
            }
        }
        else{
            db_insert("group_reads", [
                'group_id' => $group_id,
                'person' => $person_id,
                'message' => $last['id'],
            ]);
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows, JSON_INVALID_UTF8_IGNORE);
}

elseif (isset($_GET['getGroupMembers'])) {
    $group_id = $_GET['getGroupMembers'];

    $rows = getAll("group_members", ['group' => $group_id]);

    $people = get_people(
        array_column($rows, 'member')
    );
    $default_person = default_person();

    foreach ($rows as $i => $row) {
        $rows[$i]['user_data'] = $people[$row['member']] ?? $default_person;
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows);
}

elseif(isset($_POST['addMember'], $_POST['user_id'])){
    $group_id = $_POST['addMember'];

    $check = getData("group_members", [
        'group' => $group_id,
        'member' => $_POST['user_id'],
    ]);
    if ($check != null) {
        error("User is already a member");
        exit;
    }

    db_insert("group_members", [
        'group' => $group_id,
        'member' => $_POST['user_id'],
        'type' => 'member',
        'date_joined' => $time,
    ]);

    db_insert("feature_usage", [
        'user' => $student_id,
        'feature' => 'groups',
        'time' => $time,
        'date' => date('Y-m-d'),
    ]);

    db_insert("group_messages", [
        'group' => $group_id,
        'sender' => $person_id,
        'message' => '{{person_name('.$person_id.')}} added {{person_name('.$_POST['user_id'].')}}',
        'type' => 'notification',
        'time' => $time,
    ]);

    //update message count - cache
    db_update("groups", [
        'members' => db_get_count("group_members", "id", [
            'group' => $group_id
        ])
    ], ['id' => $group_id]);

    $group_data = getData("groups", ['id' => $group_id]);
    $person_data = getData("people", ['id' => $_POST['user_id']]);

    db_insert("activity_audit", [
        'user' => $user_id,
        'user_type' => 'student',
        'heading' => "Added Group Member",
        'content' => "You have added {$person_data['name']} into group: <b>{$group_data['name']}</b>",
        'time' => $time,
    ]);

    db_insert("notifications", [
        'user' => $person_data['user'],
        'user_type' => $person_data['type'],
        'title' => "You were added into a group chat",
        'content' => "You were added into a group chat by {$person_data['name']}",
        'subcontent' => "Group: {$group_data['name']}",
        'type' => 'group',
        'ref' => $group_id,
        'status' => 'unread',
        'time' => $time,
        'uri' => "group_chat.php?group_id=".$group_id,
    ]);

    //header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => true, 
        'message' => "Success",
        'name' => $person_data['name']
    ]);
}

elseif(isset($_POST['sendGroupMessage'], $_POST['group_id'], $_POST['message'])){
    db_insert("group_messages", [
        'group' => $_POST['group_id'],
        'sender' => $person_id,
        'message' => $_POST['message'],
        'type' => 'text',
        'time' => $time,
    ]);

    db_insert("feature_usage", [
        'user' => $student_id,
        'feature' => 'groups',
        'time' => $time,
        'date' => date('Y-m-d'),
    ]);

    //update message count - cache
    db_update("groups", [
        'messages' => db_get_count("group_messages", "id", [
            'group' => $_POST['group_id']
        ])
    ], ['id' => $_POST['group_id']]);

    echo json_encode(['status' => true, 'message' => "Success"]);
}
elseif (isset($_GET['getPeople'])) {
    register_people();

    $students = getAll("students","id");
    $staff = getAll("staff", "id");

    $default_person = [
        'id' => 0,
        'name' => 'Not found',
        'picture' => 'default_avatar.png'
    ];

    $rows = getAll("people");
    foreach ($rows as &$row) {
        $row['user_data'] = $row['type'] == "student" ? ($students[$row['user']] ?? $default_person) : ($staff[$row['user']] ?? $default_person);
        unset($row['user_data']['password']);
        $row['user_data']['picture'] = $row['user_data']['picture'] ?? $row['user_data']['photo'];
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($rows);
}
