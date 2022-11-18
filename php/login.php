<?php

include './db.php';
include './session.php';

function login($username, $password) {
    init_db();
    
    $conn = get_connection();
    $stmt = $conn->prepare("SELECT id, username, password FROM panneer.user WHERE username=? AND password=?;");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $data = $stmt->get_result();

    if ($data->num_rows <= 0) {
        return array(
            "message" => "User does not exists.",
            "status" => false,
        );
    }
    
    $user = $data->fetch_assoc();
    $token = generate_session($conn, $user['id']);
    if (!$token) {
        return array(
            "token" => $token,
            "message" => "Unable to login ! (Token).",
            "status" => false,
        );
    }
    
    unset($user['password']);
    $user['token'] = $token;
    $user['status'] = true;
    
    close_connection($conn);
    
    return $user;
}

$body = json_decode(file_get_contents('php://input'), true);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo json_encode(login($body['username'], $body['password']));
}

?>