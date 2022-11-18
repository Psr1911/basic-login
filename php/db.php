<?php

require_once __DIR__ . '/../vendor/autoload.php';

function get_connection() {
    $servername = "localhost";
    $username = "root";
    $password = "Panneer@2001";
    $conn = new mysqli($servername, $username, $password);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function close_connection($conn) {
    mysqli_close($conn);
}

function init_user($conn) {
    $stmt = $conn->prepare("CREATE TABLE `panneer`.`user` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `username` VARCHAR(45) NOT NULL,
            `password` VARCHAR(45) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
            UNIQUE INDEX `username_UNIQUE` (`username` ASC) VISIBLE);");
    try {
        $stmt->execute();
        $stmt->get_result();
    }
    catch (Exception $e) {}           
}

function init_session($conn) {
    $stmt = $conn->prepare("CREATE TABLE if not exists `panneer`.`session` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `user` INT NOT NULL,
                `expiry` DATETIME NOT NULL,
                `token` VARCHAR(250) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
                UNIQUE INDEX `username_UNIQUE` (`user` ASC) VISIBLE,
                UNIQUE INDEX `token_UNIQUE` (`token` ASC) VISIBLE,
                CONSTRAINT `user`
                FOREIGN KEY (`user`)
                REFERENCES `panneer`.`user` (`id`)
                ON DELETE RESTRICT
                ON UPDATE NO ACTION);
            ");
    try {
        $stmt->execute();
        $stmt->get_result();
    }
    catch (Exception $e) {}      
}

function init_db() {
    $conn = get_connection();
    init_user($conn);
    init_session($conn);
    close_connection($conn);
}

function get_mongo_client() {
    $client = new MongoDB\Client("mongodb://localhost:27017/panneer");
    return $client->panneer;
}

?>