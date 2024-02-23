<?php
require "../utils/sql.php";

session_start();

$loggedIn = false;
$uuid = -1;
$permissionLevel = 0;

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>


<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Home</a>
        <?php
        if($permissionLevel >= 1){
            echo '<a class="navbar-brand" href="tasks/edit_task.php">Create Task</a>';
        }
        if($permissionLevel >= 2){
            echo '<a class="navbar-brand" href="manage.php">Manage</a>';

        }
        ?>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <?php
                    if($loggedIn){
                        echo '<a class="nav-link" href="password_reset.php">Change Password</a>';
                        echo '</li>';
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="logout.php">Logout</a>';
                    } else {
                        echo '<a class="nav-link" href="login.php">Login</a>';
                    }
                    ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-23 col-md-16 col-lg-12 col-xl-10">
            <div class="card bg-dark text-white" style="border-radius: 1rem;">
                <div class="card-body p-3 text-center">
                    <?php
                       echo '<h2> Welcome ' . getUsername($uuid) . ', here are your tasks.</h2>'
                    ?>
                    <div class="row">
                    <?php
                    $tasks = getUsersTasks($uuid, false);
                    if($tasks) printTasks($tasks, $uuid, $permissionLevel);

                    $tasks = getUsersTasks($uuid, true);
                    if($tasks) printTasks($tasks, $uuid, $permissionLevel);

                    function printTasks($tasks, $uuid, $permissionLevel){
                        foreach ($tasks as $key => $task){
                            //Card Headers
                            echo '<div class="col-sm-6">';
                            $bgType = "";
                            switch ($task["priority"]){
                                default: {
                                    echo '<div class="card text-white bg-danger mb-3">';
                                    $bgType = "bg-danger";
                                    break;
                                }
                                case 2: {
                                    echo '<div class="card text-white bg-success mb-3">';
                                    $bgType = "bg-success";
                                    break;
                                }
                                case 3: {
                                    echo '<div class="card text-white bg-secondary mb-3">';
                                    $bgType = "bg-secondary";
                                    break;
                                }
                            }
                            echo '<div class="card-body">';
                            //Card title
                            echo '<h5 class="card-title justify-content-center">' . $task["title"] . "</h5>";
                            //Card Text
                            echo '<p class="card-text justify-content-center">' . $task["content"] . '</p>';
                            echo '</div>';
                            //List items for status, owner and completion date
                            echo '<ul class="list-group list-group-flush">';
                            echo '<li class="list-group-item '. $bgType. ' text-white text-start">Status: '. statusIntToStr($task["status"]) .'</li>';
                            echo '<li class="list-group-item '. $bgType. ' text-white text-start">Owner: '. getUsername($task["owner"]) .'</li>';
                            echo '<li class="list-group-item '. $bgType. ' text-white text-start">Completion Date: '. $task["completion_date"] .'</li>';
                            echo '</ul>';
                            //Edit button
                            if($uuid == $task["owner"] || $permissionLevel >= 2) {
                                echo '<div class="card-body">';
                                echo '<a href="./tasks/edit_task.php?method=edit&uuid=' . $key . '" class="btn btn-primary mx-1">✏️</a>';
                                echo '<a href="./tasks/delete_task.php?uuid=' . $key . '" class="btn btn-danger mx-1">🗑️</a>';
                                echo '</div>';
                            }
                            //Card closers
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
