<?php

echo "Tasks:<br>";

echo json_encode(getTasks()) . "<br>";

echo "<br>Users:<br>";

echo json_encode(getUsers()) . "<br>";

function getConn(){
    $server_name = "localhost";
    $username = "program_r";
    $password = "program_r";

    $conn = new mysqli($server_name, $username, $password);

    if($conn->connect_error){
        die("Connection failed" . $conn->connect_error);
    }

    return $conn;
}

function getTasks(){
    $conn = getConn();
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
    $conn = getConn();
    $sql = "SELECT uuid, username, password_reset, permission_level FROM assesment_2.users;";
    $result = $conn->query($sql);

    $output = array();

    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            $data = [
                "username" => $row["username"],
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
