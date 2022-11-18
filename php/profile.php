<?php

include './db.php';
include './session.php';

function get_profile($user_id) {
    if (!$user_id) {
        return Array(
            "status" => false,
            "message" => "Invlalid Token."
        );
    }
    
    $client = get_mongo_client();
    $profiles = $client->profile;
    $cursor = $profiles->findOne(["user" => $user_id], []);
    
    if ($cursor['user'] !== $user_id) {
        return Array(
            "user" => $user_id,
            "status" => false,
            "message" => "Profile not found."
        );
    }

    unset($cursor['_id']);
    $cursor['user'] = $user_id;
    $cursor['status'] = true;
    
    return $cursor;
}

function update_profile($user_id, $data) {
    if (!$user_id) {
        return Array(
            "status" => false,
            "message" => "Invlalid Token."
        );
    }

    $client = get_mongo_client();
    $profiles = $client->profile;
    $d = [
        'age' => (int)$data['age'],
        'dob' => $data['dob'],
        'phone' => $data['phone'],
        ];
    $cursor = $profiles->updateOne(["user" => $user_id], ['$set' => $d]);

    if ($cursor->getModifiedCount() !== 1) {
        return Array(
            "user" => $user_id,
            "status" => false,
            "message" => "Unable to update."
        );
    }

    $d['user'] = $user_id;
    $d['status'] = true;
    
    return $d;
}

$headers = apache_request_headers();
$body = json_decode(file_get_contents('php://input'), true);

$token = $headers['Authorization'];
$conn = get_connection();
$user_id = (int)get_session($conn, $token);
close_connection($conn);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo json_encode(get_profile($user_id));
}

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    echo json_encode(update_profile($user_id, $body));
}

?>