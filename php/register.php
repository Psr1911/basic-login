<?php

include './db.php';
include './session.php';

function register($username, $password, $confirm_password) {
    init_db();
    
    if ($password !== $confirm_password) {
        return array(
            "message" => "confirm password does not match.",
            "status" => false,
        );
    }

    $conn = get_connection();
    try {
        $stmt = $conn->prepare("SELECT id FROM panneer.user WHERE username=?;");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $data = $stmt->get_result();

        $result = null;
        
        if ($data->num_rows > 0) {
            return array(
                "message" => "User already exists.",
                "status" => false,
            );
        }
    }
    catch (Exception $e) {}
    
    $stmt = $conn->prepare("INSERT INTO panneer.user (username, password)
                        VALUES (?, ?);");
    $stmt->bind_param("ss", $username, $password);
    
    try {
        $stmt->execute();
        $stmt->get_result();
    }
    catch (Exception $e) {
        return array(
            "message" => "Unable to register.",
            "status" => false,
        );
    }
    
    $last_id = $stmt->insert_id;
    $user = array("username" => $username);
    $token = generate_session($conn, $last_id);
    if (!$token) {
        return array(
            "message" => "unable to register !",
            "status" => false,
        );
    }

    $client = get_mongo_client();
    $profiles = $client->profile;
    $insertOneResult = $profiles->insertOne([
        'user' => $last_id,
        'age' => null,
        'dob' => null,
        'phone' => null,
     ]);
    
    unset($user['password']);
    $user['token'] = $token;
    $user['status'] = true;
    
    close_connection($conn);
    
    return $user;
}

$body = json_decode(file_get_contents('php://input'), true);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo json_encode(register($body['username'], $body['password'], $body['confirm_password']));
}

?>