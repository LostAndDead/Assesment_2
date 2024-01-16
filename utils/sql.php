<?php

function getReadonlyConn(){
    $server_name = "localhost";
    $username = "program_r";
    $password = "program_r";

    $conn = new mysqli($server_name, $username, $password);

    if($conn->connect_error){
        die("Connection failed" . $conn->connect_error);
    }

    return $conn;
}

function getWriteConn(){
    $server_name = "localhost";
    $username = "program";
    $password = "program";

    $conn = new mysqli($server_name, $username, $password);

    if($conn->connect_error){
        die("Connection failed" . $conn->connect_error);
    }

    return $conn;
}

function getTasks(){
    $conn = getReadonlyConn();
    $sql = "SELECT * FROM assesment_2.tasks;";
    $result = $conn->query($sql);

    $output = array();

    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            $data = [
                "title" => $row["title"],
                "content" => $row["content"],
                "status" => $row["status"],
                "owner" => $row["owner"],
                "completion_date" => $row["completion_date"]
            ];
            $output[$row["uuid"]] = $data;
        }
    } else {
        echo "0 Results";
    }

    $conn->close();
    return $output;
}

function getUsers(){
    $conn = getReadonlyConn();
    $sql = "SELECT uuid, username, password_reset, permission_level, email FROM assesment_2.users;";
    $result = $conn->query($sql);

    $output = array();

    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            $data = [
                "username" => $row["username"],
                "email" => $row["email"],
                "password_reset" => $row["password_reset"],
                "permission_level" => $row["permission_level"],
            ];
            $output[$row["uuid"]] = $data;
        }
    } else {
        echo "0 Results";
    }

    $conn->close();
    return $output;
}

function getUser($email){
    $conn = getReadonlyConn();
    $sql = "SELECT * FROM assesment_2.users WHERE email = ?;";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $email);
    $sqlStatement->execute();
    $result = $sqlStatement->get_result();

    $output = array();

    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            $data = [
                "username" => $row["username"],
                "email" => $row["email"],
                "password_hash" => $row["password_hash"],
                "password_reset" => $row["password_reset"],
                "permission_level" => $row["permission_level"],
            ];
            $output[$row["uuid"]] = $data;
        }
    } else {
        return null;
    }

    $conn->close();
    return $output;
}

function createUser($username, $email, $password, $passwordReset){
    $conn = getWriteConn();
    $sql = "INSERT INTO assesment_2.users (uuid, username, email, password_hash, password_reset) VALUES (uuid(), ?, ?, ?, ?);";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("sssb", $username, $email, $password, $passwordReset);
    try {
        $sqlStatement->execute();
        return "Worked";
    } catch (mysqli_sql_exception $exception) {
        $msg = $exception->getCode();
        if($msg = 1062){
            return "Duplicate";
        }
    }
}
