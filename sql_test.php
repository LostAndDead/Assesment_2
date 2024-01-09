<?php

$server_name = "localhost";
$username = "program_r";
$password = "program_r";

$conn = new mysqli($server_name, $username, $password);

if($conn->connect_error){
    die("Connection failed" . $conn->connect_error);
}

echo "Tasks:";

$sql = "SELECT * FROM assesment_2.tasks";
$result = $conn->query($sql);

if($result->num_rows > 0){
    while ($row = $result->fetch_assoc()){
        $data = $row["uuid"] . " : ". $row["title"] . " : " . $row["content"];
        echo "<p>$data</p>";
    }
} else {
    echo "0 Results";
}

echo "Users:";

$sql = "SELECT username, uuid, permission_level FROM assesment_2.users;";
$result = $conn->query($sql);

if($result->num_rows > 0){
    while ($row = $result->fetch_assoc()){
        $data = $row["uuid"] . " : ". $row["username"] . " : " . $row["permission_level"];
        echo "<p>$data</p>";
    }
} else {
    echo "0 Results";
}

$conn->close();