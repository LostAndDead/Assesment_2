<?php
require "../../utils/sql.php";

session_start();

$loggedIn = false;
$uuid = -1;
$permissionLevel = 0;
$taskUUID = -1;

if(!empty($_SESSION["uuid"])){
    $uuid = $_SESSION["uuid"];
    $sessionUUID = $_SESSION["session_uuid"];
    $valid = checkSession($uuid, $sessionUUID);
    if ($valid) {
        $loggedIn = true;
        $uuid = $_SESSION["uuid"];
        $permissionLevel = getPermissionLevel($uuid);
        $passwordChange = getPasswordChange($uuid);
        if($passwordChange){
            header("Location: ./password_reset.php");
        }
    }

}

if(!$loggedIn){
    header("Location: ./login.php");
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["uuid"])){
        $taskUUID = test_input($_POST["uuid"]);
        $task = getTask($taskUUID, $uuid);
        if($task["owner"] == $uuid || $permissionLevel >= 2){
            $res = setDeleteTask($taskUUID, true);
            header("Location: ../homepage.php");
        } else {
            header("Location: ../homepage.php");
        }

    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET"){
    if (!empty($_GET["uuid"])){
        $taskUUID = test_input($_GET["uuid"]);
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Delete A Task</title>
    <link rel="stylesheet" href="../../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../homepage.php">Home</a>
        <?php
        if($permissionLevel >= 1){
            echo '<a class="navbar-brand" href="./edit_task.php">Create Task</a>';
        }
        if($permissionLevel >= 2){
            echo '<a class="navbar-brand" href="../manage.php">Manage</a>';

        }
        ?>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <?php
                    if($loggedIn){
                        echo '<a class="nav-link" href="../password_reset.php">Change Password</a>';
                        echo '</li>';
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="../logout.php">Logout</a>';
                    } else {
                        echo '<a class="nav-link" href="../login.php">Login</a>';
                    }
                    ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5 h-75">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card bg-dark text-white" style="border-radius: 1rem;">
                <div class="card-body p-2 text-center">
                    <div class="mb-md-2 mt-md-2 pb-2">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <h2>Are you sure?</h2>
                            <?php
                            echo '<input type="hidden" name="uuid" value="' . $taskUUID . '">'
                            ?>
                            <button class="btn btn-outline-light btn-lg px-5" type="submit" name="submit">Confirm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>