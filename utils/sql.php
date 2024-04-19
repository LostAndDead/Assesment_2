<?php

/**
 *
 * Gets a read only connection to the database
 *
 * @return mysqli
 *
 */
function getReadonlyConn(): mysqli
{
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
function getWriteConn(): mysqli
{
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
function getTasks(): array
{
    $conn = getReadonlyConn();
    $sql = "SELECT * FROM assesment_2.tasks ORDER BY priority;";
    $result = $conn->query($sql);

    $output = array();

    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            $data = [
                "title" => $row["title"],
                "content" => $row["content"],
                "status" => $row["status"],
                "owner" => $row["owner"],
                "completion_date" => $row["completion_date"],
                "priority" => $row["priority"],
                "deleted" => $row["deleted"]
            ];
            $output[$row["uuid"]] = $data;
        }
    }

    $conn->close();
    return $output;
}

/**
 *
 * Fetch a list of all the tasks from the database who do or don't belong to a user
 *
 * @param string $uuid The users who tasks to get
 * @param bool $not True for not users tasks, False for users tasks
 *
 * @return array
 *
 */
function getUsersTasks(string $uuid, bool $not): ?array
{
    $conn = getReadonlyConn();
    if($not){
        $sql = "SELECT * FROM assesment_2.tasks where owner <> ? AND deleted = false ORDER BY priority;";
    } else {
        $sql = "SELECT * FROM assesment_2.tasks where owner = ? AND deleted = false ORDER BY priority;";
    }

    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $uuid);
    $sqlStatement->execute();

    $output = array();

    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            while ($row = $result->fetch_assoc()){
                $data = [
                    "title" => $row["title"],
                    "content" => $row["content"],
                    "status" => $row["status"],
                    "owner" => $row["owner"],
                    "completion_date" => $row["completion_date"],
                    "priority" => $row["priority"]
                ];
                $output[$row["uuid"]] = $data;
            }
        } else {
            return null;
        }
    } catch (mysqli_sql_exception $exception) {
        return null;
    }

    $conn->close();
    return $output;
}

/**
 *
 * Delete a task (or un delete it)
 *
 * @param string $taskUUID The uuid of the task to delete
 * @param bool $state The state for the task, deleted or not
 *
 * @return bool True for delete False for error
 *
 */
function setDeleteTask(string $taskUUID, bool $state): bool
{
    $conn = getWriteConn();
    $sql = "UPDATE assesment_2.tasks SET deleted = ? WHERE UUID = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("is", $state, $taskUUID);
    try {
        $sqlStatement->execute();
        $conn->close();
        return true;
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
        return false;
    }

}

/**
 *
 * Fetches all the users from the database
 * Password data is omitted
 *
 * @return array
 *
 */
function getAllUsers(): array
{
    $conn = getReadonlyConn();
    $sql = "SELECT uuid, username, password_reset, permission_level, email, active FROM assesment_2.users;";
    $result = $conn->query($sql);

    $output = array();

    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            $data = [
                "username" => $row["username"],
                "email" => $row["email"],
                "password_reset" => $row["password_reset"],
                "permission_level" => $row["permission_level"],
                "active" => $row["active"]
            ];
            $output[$row["uuid"]] = $data;
        }
    }

    $conn->close();
    return $output;
}

/**
 *
 * Gets a users full data by their email
 *
 * @param string $uuid The uuid to query the user for.
 *
 * @return array
 *
 */
function getUser(string $uuid): ?array
{
    $conn = getReadonlyConn();
    $sql = "SELECT * FROM assesment_2.users WHERE uuid = ?;";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $uuid);
    $sqlStatement->execute();
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $res = $result->fetch_assoc();
            $conn->close();
            return $res;
        } else {
            $conn->close();
            return null;
        }
    } catch (mysqli_sql_exception $exception) {
        return null;
    }
}

/**
 *
 * Update a users basic information based on uuid
 *
 * @param string $uuid The uuid to update for
 * @param int $permLevel The permission level to set for the user
 * @param bool $active The active status, false is disabled
 *
 * @return bool
 */
function patchUser(string $uuid, int $permLevel, bool $active): bool
{
    $conn = getWriteConn();
    $sql = "UPDATE assesment_2.users SET permission_level = ?, active = ?, last_updated = now() WHERE UUID = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("sis", $permLevel, $active, $uuid);
    try {
        $sqlStatement->execute();
        $conn->close();
        return true;
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
        return false;
    }
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
function createUser(string $username, string $email, string $password, bool $passwordReset): string
{
    $conn = getWriteConn();
    $sql = "INSERT INTO assesment_2.users (uuid, username, email, password_hash, password_reset, last_updated) VALUES (uuid(), ?, ?, ?, ?, now());";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("sssi", $username, $email, $password, $passwordReset);
    try {
        $sqlStatement->execute();
        $conn->close();
        return "Worked";
    } catch (mysqli_sql_exception $exception) {
        $code = $exception->getCode();
        if($code == 1062){
            $conn->close();
            return "Duplicate";
        }
    }
    $conn->close();
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
function checkLogin(string $email, string $password): bool
{
    $conn = getReadonlyConn();
    $sql = "SELECT password_hash FROM assesment_2.users WHERE email = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $email);
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $conn->close();
            return password_verify($password, $row["password_hash"]);
        } else {
            $conn->close();
            return false;
        }
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
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
function getUUID(string $email) {
    $conn = getReadonlyConn();
    $sql = "SELECT uuid FROM assesment_2.users WHERE email = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $email);
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $conn->close();
            return $row["uuid"];
        } else {
            $conn->close();
            return false;
        }
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
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
function getPermissionLevel(string $uuid){
    $conn = getReadonlyConn();
    $sql = "SELECT permission_level FROM assesment_2.users WHERE uuid = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $uuid);
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $conn->close();
            return $row["permission_level"];
        } else {
            $conn->close();
            return false;
        }
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
        return false;
    }
}

/**
 *
 * Gets if the user needs to change their password from their UUID
 *
 * @param string $uuid The UUID to query
 *
 * @return int
 *
 */
function getPasswordChange(string $uuid){
    $conn = getReadonlyConn();
    $sql = "SELECT password_reset FROM assesment_2.users WHERE uuid = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $uuid);
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $conn->close();
            return $row["password_reset"];
        } else {
            $conn->close();
            return false;
        }
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
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
function getUsername(string $uuid){
    $conn = getReadonlyConn();
    $sql = "SELECT username FROM assesment_2.users WHERE uuid = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $uuid);
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $conn->close();
            return $row["username"];
        } else {
            $conn->close();
            return false;
        }
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
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
function changePassword(string $uuid, string $password): bool
{
    $conn = getWriteConn();
    $sql = "UPDATE assesment_2.users SET password_hash = ?, password_reset=false, last_updated = now() WHERE UUID = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("ss", $password, $uuid);
    try {
        $sqlStatement->execute();
        $conn->close();
        return true;
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
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
 * @param int $prio The priority of the new task
 * @param int $completionDate The completion date of the new task
 * @param string $uuid The UUID of the user creating the task
 *
 * @return bool True for successful, false for failed
 */
function createTask(string $title, string $content, int $status, int $prio, int $completionDate, string $uuid): bool
{
    $conn = getWriteConn();
    $sql = "INSERT INTO assesment_2.tasks (uuid, title, content, status, priority, owner, completion_date, last_updated) VALUES (uuid(),?,?,?,?,?,?,now()";
    $date = date("Y-m-d H:i:s", $completionDate);
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("ssiiss", $title, $content, $status, $prio, $uuid, $date);
    try {
        $sqlStatement->execute();
        $conn->close();
        return true;
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
        return false;
    }
}

/**
 *
 * Update a task
 *
 * @param string $taskUUID The UUID of the task to update
 * @param string $title The title of the  task
 * @param string $content The content of the task
 * @param int $status The Status of the task
 * @param int $prio The priority of the task
 * @param int $completionDate The completion date of the new task
 * @param string $uuid The UUID of the user creating the task
 *
 * @return bool True for successful, false for failed
 */
function updateTask(string $taskUUID, string $title, string $content, int $status, int $prio, int $completionDate, string $uuid): bool
{
    $conn = getWriteConn();
    $sql = "UPDATE assesment_2.tasks SET title=?, content=?, status=?, priority=?, owner=?, completion_date=?, last_updated = now() WHERE uuid=?";
    $date = date("Y-m-d H:i:s", $completionDate);
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("ssiisss", $title, $content, $status, $prio, $uuid, $date, $taskUUID);
    try {
        $sqlStatement->execute();
        $conn->close();
        return true;
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
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
function getTask(string $taskUUID, string $userUUID): ?array
{
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
                "completion_date" => $row["completion_date"],
                "priority" => $row["priority"]
            ];
            $output[$row["uuid"]] = $data;
        }
    } else {
        $conn->close();
        return null;
    }
    $conn->close();

    $perm_level = getPermissionLevel($userUUID);
    if($userUUID == $output[$taskUUID]["owner"] || $perm_level >= 2){
        return $output;
    }
    return null;
}

/**
 *
 * Generates a new session uuid for a user and returns it
 *
 * @param string $uuid the uuid for the use to generate the session for
 *
 * @return string The new session id for the user
 */
function generateSession(string $uuid): string
{
    $conn = getWriteConn();
    $sql = "UPDATE assesment_2.users set session_uuid = uuid(), last_updated = now() WHERE uuid = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $uuid);
    $sqlStatement->execute();
    $conn->close();

    $user = getUser($uuid);
    return $user["session_uuid"];
}

function checkSession($uuid, $session): bool
{
    $conn = getReadonlyConn();
    $sql = "SELECT session_uuid FROM assesment_2.users WHERE uuid=?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $uuid);
    try {
        $sqlStatement->execute();
        $result = $sqlStatement->get_result();
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $conn->close();
            return password_verify($row["session_uuid"], $session);
        } else {
            $conn->close();
            return false;
        }
    } catch (mysqli_sql_exception $exception) {
        $conn->close();
        return false;
    }
}

function destroySession($uuid){
    $conn = getWriteConn();
    $sql = "UPDATE assesment_2.users set session_uuid = -1, last_updated = now() WHERE uuid = ?";
    $sqlStatement = $conn->prepare($sql);
    $sqlStatement->bind_param("s", $uuid);
    $sqlStatement->execute();
    $conn->close();
}

/**
 *
 * Convert a status string to its int equivalent
 *
 * @param string $status Status to change
 *
 * @return int The ID of the status
 */
function statusStrToInt(string $status): int
{
    switch ($status){
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
function statusIntToStr(int $status): string
{
    switch ($status){
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

enum EventType{
    case LOGGED_IN;
    case LOGGED_OUT;
    case TASK_CREATED;
    case TASK_EDITED;
    case TASK_DELETED;
    case USER_CREATED;
    case USER_UPDATED;
    case USER_PASSWORD_RESET;

    public function value(): string
    {
        return match($this) {
            EventType::LOGGED_IN => "LOGGED_IN",
            EventType::LOGGED_OUT => "LOGGED_OUT",
            EventType::TASK_CREATED => "TASK_CREATED",
            EventType::TASK_EDITED => "TASK_EDITED",
            EventType::TASK_DELETED => "TASK_DELETED",
            EventType::USER_CREATED => "USER_CREATED",
            EventType::USER_UPDATED => "USER_UPDATED",
            EventType::USER_PASSWORD_RESET => "USER_PASSWORD_RESET",
        };
    }
}

function logAction($uuid, EventType $action, $ip, $extraDetails = ""): void
{
    $conn = getWriteConn();
    $sql = "INSERT INTO assesment_2.logs (user_uuid, event, extra_details, ip_addr) VALUES (?, ?, ?, ?);";
    $sqlStatement = $conn->prepare($sql);
    $actionValue = $action->value();
    $sqlStatement->bind_param("ssss", $uuid, $actionValue, $extraDetails, $ip);
    $sqlStatement->execute();
    $conn->close();
}