<?php

/**
 *
 * Gets a read only connection to the database
 *
 * @return mysqli
 *
 */
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

/**
 *
 * Gets a write permission connection to the database
 *
 * @return mysqli
 *
 */
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

/**
 *
 * Fetch a list of all the tasks from the database
 *
 * @return array
 *
 */
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

/**
 *
 * Fetches all the users from the database
 * Password data is omitted
 *
 * @return array
 *
 */
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

/**
 *
 * Gets a users full data by their email
 *
 * @param string $email The email to query the user for.
 *
 * @return array
 *
 */
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

/**
 *
 * Create a new user in the database
 *
 * @param string $username The username of the user to add
 * @param string $email The email address of the user to add
 * @param string $password The hashed password for the user
 * @param bool $passwordReset Whether the user will be forced to reset their password
 *
 * @return string The result as a string (Worked | Duplicate | shrug)
 *
 */
function createUser($username, $email, $password, $passwordReset){
    $conn = getWriteConn();
    $sql = "INSERT INTO assesment_2.users (uuid, username, email, password_hash, password_reset) VALUES (uuid(), ?, ?, ?, ?);";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("sssb", $username, $email, $password, $passwordReset);
    try {
        $sqlStatement->execute();
        return "Worked";
    } catch (mysqli_sql_exception $exception) {
        $code = $exception->getCode();
        if($code = 1062){
            return "Duplicate";
        }
    }
    return "shrug";
}

/**
 *
 * Checks a users login credentials
 *
 * @param string $email The email to query the user for.
 * @param string $password Plain text password for comparison
 *
 * @return bool True for valid, False for invalid or error
 *
 */
function checkLogin($email, $password) {
    $conn = getReadonlyConn();
    $sql = "SELECT password_hash FROM assesment_2.users WHERE email = ?;";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $email);
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            return password_verify($password, $row["password_hash"]);
        } else {
            return false;
        }
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

/**
 *
 * Gets a users UUID from their email
 *
 * @param string $email The email to query
 *
 * @return string
 *
 */
function getUUID($email) {
    $conn = getReadonlyConn();
    $sql = "SELECT uuid FROM assesment_2.users WHERE email = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $email);
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            return $row["uuid"];
        } else {
            return false;
        }
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

/**
 *
 * Gets a users Permission Level from their UUID
 *
 * @param string $uuid The UUID to query
 *
 * @return int
 *
 */
function getPermissionLevel($uuid){
    $conn = getReadonlyConn();
    $sql = "SELECT permission_level FROM assesment_2.users WHERE uuid = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $uuid);
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            return $row["permission_level"];
        } else {
            return false;
        }
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

/**
 *
 * Gets a users Username from their UUID
 *
 * @param string $uuid The UUID to query
 *
 * @return int
 *
 */
function getUsername($uuid){
    $conn = getReadonlyConn();
    $sql = "SELECT username FROM assesment_2.users WHERE uuid = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $uuid);
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            return $row["username"];
        } else {
            return false;
        }
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

/**
 *
 * Change a users password
 *
 * @param string $uuid The UUID of the user to change
 * @param string $password The new Hashed password
 *
 * @return bool True for valid, False for error
 *
 */
function changePassword($uuid, $password){
    $conn = getWriteConn();
    $sql = "UPDATE assesment_2.users SET password_hash = ? WHERE UUID = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("ss", $password, $uuid);
    try {
        $sqlStatement->execute();
        return true;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

/**
 *
 * Create a new task
 *
 * @param string $title The title of the new task
 * @param string $content The content of the new task
 * @param int $status The Status of the new task
 * @param int $completionDate The completion date of the new task
 * @param string $uuid The UUID of the user creating the task
 *
 * @return bool True for successful, false for failed
 */
function createTask($title, $content, $status, $completionDate, $uuid){
    $conn = getWriteConn();
    $sql = "INSERT INTO assesment_2.tasks (uuid, title, content, status, owner, completion_date) VALUES (uuid(),?,?,?,?,?)";
    $date = date("Y-m-d H:i:s", $completionDate);
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("ssiss", $title, $content, $status, $uuid, $date);
    try {
        $sqlStatement->execute();
        return true;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

/**
 *
 * Update a task
 *
 * @param string $taskUUID The UUID of the task to update
 * @param string $title The title of the new task
 * @param string $content The content of the new task
 * @param int $status The Status of the new task
 * @param int $completionDate The completion date of the new task
 * @param string $uuid The UUID of the user creating the task
 *
 * @return bool True for successful, false for failed
 */
function updateTask($taskUUID, $title, $content, $status, $completionDate, $uuid){
    $conn = getWriteConn();
    $sql = "UPDATE assesment_2.tasks SET title=?, content=?, status=?, owner=?, completion_date=? WHERE uuid=?";
    $date = date("Y-m-d H:i:s", $completionDate);
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("ssisss", $title, $content, $status, $uuid, $date, $taskUUID);
    try {
        $sqlStatement->execute();
        return true;
    } catch (mysqli_sql_exception $exception) {
        return false;
    }
}

/**
 *
 * Get a task from its UUID, will only work if user has permission
 *
 * @param string $taskUUID
 * @param string $userUUID
 *
 * @return array The task object
 */
function getTask($taskUUID, $userUUID){
    $conn = getReadonlyConn();
    $sql = "SELECT * FROM assesment_2.tasks WHERE uuid=?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $taskUUID);
    $sqlStatement->execute();
    $result = $sqlStatement->get_result();

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
        return null;
    }
    $conn->close();

    $perm_level = getPermissionLevel($userUUID);
    if($userUUID != $output[$taskUUID]["owner"] && $perm_level < 2){
        return null;
    }
    return $output;
}

/**
 *
 * Convert a status string to its int equivalent
 *
 * @param string $status Status to change
 *
 * @return int The ID of the status
 */
function statusStrToInt($status){
    switch ($status){
        case "Not started": {
            return 1;
        }
        case "Started": {
            return 2;
        }
        case "On hold": {
            return 3;
        }
        case "Completed": {
            return 4;
        }
        default: {
            return 1;
        }
    }
}

/**
 *
 * Convert a status string to its int equivalent
 *
 * @param int $status Status ID to get
 *
 * @return string The text of the status
 */
function statusIntToStr($status){
    switch ($status){
        case 1: {
            return "Not started";
        }
        case 2: {
            return "Started";
        }
        case 3: {
            return "On hold";
        }
        case 4: {
            return "Completed";
        }
        default: {
            return "Not started";
        }
    }
}