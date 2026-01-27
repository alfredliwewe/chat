<?php
session_start();
require "../includes/String.php";
require_once "../functions.php";
$db = new mysql_like("../db.db");
require_once "../config.php";

$time = time();

if(isset($_POST['user_login'], $_POST['email'], $_POST['password'])){
    $user = null;
    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $user = getData("users", [
            'email' => $_POST['email']
        ]);
    }else{
        $user = getData("users", [
            'phone' => $_POST['email']
        ]);
    }
    if($user){
        if(md5($_POST['password']) == $user['password']){
            $_SESSION['user'] = $user;
            $_SESSION['user_id'] = $user['id'];

            setcookie("chat_user_id", $user['id'], $time + (3600 * 24 * 30), "./");

            echo json_encode([
                'status' => true,
                'message' => 'Login successful',
                'data' => $user
            ]);
        }else{
            echo json_encode([
                'status' => false,
                'message' => 'Invalid password',
                'type' => 'password'
            ]);
        }
    }else{
        echo json_encode([
            'status' => false,
            'message' => 'User not found',
            'type' => 'email'
        ]);
    }
}
elseif(isset($_POST['fullname'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])){
    $check_email = getData("users", [
        'email' => $_POST['email']
    ]);
    if($check_email){
        echo json_encode([
            'status' => false,
            'message' => 'Email already exists',
            'type' => 'email'
        ]);
        exit;
    }

    $check_phone = getData("users", [
        'phone' => $_POST['email']
    ]);
    if($check_phone){
        echo json_encode([
            'status' => false,
            'message' => 'Phone already exists',
            'type' => 'phone'
        ]);
        exit;
    }
    $id = db_insert("users", [
        'name' => $_POST['fullname'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'] ?? "",
        'password' => md5($_POST['password']),
        'status' => 'active',
        'date_registered' => $time,
        'picture' => 'default_avatar.png'
    ]);

    $data = getData("users", [
        'id' => $id
    ]);

    $_SESSION['user'] = $data;
    $_SESSION['user_id'] = $data['id'];

    echo json_encode([
        'status' => true,
        'message' => 'User created successfully',
        'data' => $data
    ]);
}
else{
    echo json_encode([
        'status' => false,
        'message' => 'Invalid request',
        'post' => $_POST,
        'get' => $_GET
    ]);
}