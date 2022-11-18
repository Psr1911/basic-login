<?php

function generate_token($length = 10) {
    init_db();
    
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generate_session($conn, $user_id) {
    init_db();
    
    $token = generate_token(200);    
    $expiry = date('Y-m-d H:i:s', strtotime("+2 days"));
    
    try {
        $stmt = $conn->prepare("INSERT INTO panneer.session (user, expiry, token)
                                VALUES (?, ?, ?);");
        $stmt->bind_param("sss", $user_id, $expiry, $token);
        $stmt->execute();
        $data = $stmt->get_result();
        
        return $token;
    }
    catch (Exception $e) {
        return false;
        
    }
    return false;
}

function get_session($conn, $token) {
    $stmt = $conn->prepare("SELECT id, user, expiry FROM panneer.session WHERE token=?;");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $data = $stmt->get_result();

    if ($data->num_rows <= 0) {
        return false;
    }

    $session = $data->fetch_assoc();
    $user = $session['user'];

    $now = new DateTime();
    $expiry = new DateTime($session['expiry']);
    $interval = $now->diff($expiry);
    $diff_days = (int)$interval->format('%R%a');
    
    if ($diff_days < 0) {
        return false;
    }

    return $user;
}

?>