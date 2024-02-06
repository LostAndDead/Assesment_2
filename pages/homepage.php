<?php
require "../utils/sql.php";

session_start();

$loggedIn = false;
$uuid = -1;
$permissionLevel = 0;

if(!empty($_SESSION["uuid"])){
    $loggedIn = true;
    $uuid = $_SESSION["uuid"];
    $permissionLevel = getPermissionLevel($uuid);
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
        if($permissionLevel == 2){
            echo '<a class="navbar-brand" href="/manage.php">Manage</a>';
            echo '<a class="navbar-brand" href="tasks/edit_task.php">Create Task</a>';
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
                    if($loggedIn){
                        echo '<h1>Welcome ' . getUsername($uuid) . '</h1>';
                        switch ($permissionLevel){
                            case 0: {
                                echo '<h2>You are a Guest</h2>';
                                break;
                            }
                            case 1: {
                                echo '<h2>You are a User</h2>';
                                break;
                            }
                            case 2: {
                                echo '<h2>You are an Admin</h2>';
                                break;
                            }
                        }
                    } else {
                        echo '<h1>Please log in to proceed</h1>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
