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

if($permissionLevel < 2) {
    header("Location ./homepage.php");
    die();
}

$msg = $userErr = $permissionErr = "";
$userid = $active = $permLevel = "";
$page = "users";

if($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET["page"])) {
        $page = $_GET["page"];
    } else {
        if (!empty($_GET["userid"])) {
            $userid = test_input($_GET["userid"]);
            $user = getUser($userid);
            if(!$user){
                $userErr = "Invalid User";
            } else {
                if($user["active"] == 1){
                    $active = true;
                } else {
                    $active = false;
                }
                $permLevel = (int) $user["permission_level"];
            }
        }
        if (!empty($_GET["success"])) {
            $msg = "User updated.";
        }
    }


} else if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["task"])) {
        $taskUUID = $_POST["task"];
        $page = $_POST["page"];
        $res = setDeleteTask($taskUUID, false);
        logAction($uuid, EventType::TASK_EDITED, $_SERVER['REMOTE_ADDR'], 'Task: ' . $userid . ' REINSTATED');
    } else {
        if (empty($_POST["userid"])) {
            $userErr = "User is required";
        } else {
            $userid = test_input($_POST["userid"]);
        }

        if (empty($_POST["perm_level"])) {
            $permLevel = 0;
        } else {
            $permLevel = (int) test_input($_POST["perm_level"]);
        }

        if (empty($_POST["active"])) {
            $active = false;
        } else {
            $active = (bool) test_input($_POST["active"]);
        }

        if (!$userErr){
            if($userid == $uuid){
                $msg = "As a failsafe you cannot update your self.";
            } else {
                $res = patchUser($userid, $permLevel, $active);
                logAction($uuid, EventType::USER_UPDATED, $_SERVER['REMOTE_ADDR'], 'User: ' . $userid . 'PermLevel: ' . $permLevel . ' Active: ' . ($active) ? 'ACTIVE' : 'DISABLED' );
                if($res){
                    header( "refresh:1;url=./manage.php?userid=" . $userid . "&success=true");
                } else {
                    $msg = "Failed to update user, try again later.";
                }
            }

        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="./homepage.php">Home</a>
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

<script language="javascript" type="text/javascript">
    function doReload(userid){
        document.location = 'manage.php?userid=' + userid;
    }
</script>

<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-23 col-md-16 col-lg-12 col-xl-10">
            <div class="card bg-dark text-white" style="border-radius: 1rem;">
                <div class="card-body p-3 text-center">
                    <div class="mb-md-5 mt-md-4 pb-5">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <?php
                                if($page == "tasks"){
                                    echo '<button class="nav-link" id="user-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false">Users</button>';
                                } else {
                                    echo '<button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Users</button>';
                                }
                                ?>
                            </li>
                            <li class="nav-item" role="presentation">
                                <?php
                                if($page == "tasks"){
                                    echo '<button class="nav-link active" id="task-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab" aria-controls="tasks" aria-selected="true">Tasks</button>';
                                } else {
                                    echo '<button class="nav-link" id="task-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab" aria-controls="tasks" aria-selected="false">Tasks</button>';
                                }
                                ?>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <?php
                            if($page == "tasks"){
                                echo '<div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="user-tab">';
                            } else {
                                echo '<div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="user-tab">';
                            }
                            ?>
                                <h2 class="fw-bold mb-2 text-uppercase">Edit Users</h2>
                                <p class="text-white-50 mb-5">Select a user bellow to edit</p>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                <div class="form-outline form-white form-floating mb-4">
                                    <select class="form-select form-control-lg" id="typeUserX" name="user" onchange="doReload(this.value);">
                                        <option disabled selected value> -- select an option -- </option>
                                        <?php
                                            $users = getAllUsers();
                                            echo '<optgroup label="Active">';
                                            foreach($users as $key => $user){
                                                if(!$user["active"]){
                                                    continue;
                                                }
                                                if($userid == $key){
                                                    echo '<option selected value="' . $key. '">' . $user["username"] . '</option>';
                                                }else {
                                                    echo '<option value="' . $key . '">' . $user["username"] . '</option>';
                                                }
                                            }
                                            echo '</optgroup>';
                                            echo '<optgroup label="Disabled">';
                                            foreach($users as $key => $user){
                                                if($user["active"]){
                                                    continue;
                                                }
                                                if($userid == $key){
                                                    echo '<option selected value="' . $key . '">' . $user["username"] . '</option>';
                                                }else {
                                                    echo '<option value="' . $key . '">' . $user["username"] . '</option>';
                                                }
                                            }
                                            echo '</optgroup>';
                                        ?>
                                    </select>
                                    <?php
                                    if($userErr){
                                        echo '<label class="form-label-error" for="typeUserX">' . $userErr . '</label>';
                                    }else {
                                        echo '<label class="form-label" for="typeUserX">User</label>';
                                    }
                                    ?>
                                </div>
                                </form>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                    <div class="form-outline form-white form-floating mb-4">
                                        <select class="form-select form-control-lg" id="typePermX" name="perm_level"">
                                        <?php
                                            switch ($permLevel){
                                                case 0: {
                                                    echo '<option selected value=0>Guest</option>';
                                                    echo '<option value=1>User</option>';
                                                    echo '<option value=2>Admin</option>';
                                                    break;
                                                }
                                                case 1: {
                                                    echo '<option value=0>Guest</option>';
                                                    echo '<option selected value=1>User</option>';
                                                    echo '<option value=2>Admin</option>';
                                                    break;
                                                }
                                                case 2: {
                                                    echo '<option value=0>Guest</option>';
                                                    echo '<option value=1>User</option>';
                                                    echo '<option selected value=2>Admin</option>';
                                                    break;
                                                }
                                            }
                                        ?>
                                        </select>
                                        <?php
                                        if($permissionErr){
                                            echo '<label class="form-label-error" for="typePermX">' . $permissionErr . '</label>';
                                        }else {
                                            echo '<label class="form-label" for="typePermX">Permission Level</label>';
                                        }
                                        ?>
                                    </div>
                                    <div class="form-outline form-white form-floating mb-4 d-flex justify-content-center">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="active" name="active"<?php echo $active ? ' checked' : ''; ?>>
                                            <label class="form-check-label" for="active">Active</label>
                                        </div>
                                    </div>
                                    <?php
                                    echo '<input type="hidden" name="userid" value="' . $userid . '">';
                                    if($msg){
                                        echo '<div>';
                                        echo '<p class="mb-0">' . $msg . '</p>';
                                        echo '</div>';
                                    }
                                    ?>
                                    <button class="btn btn-outline-light btn-lg px-5" type="submit" name="submit">Update</button>
                                    <br>
                                    <a href="./register.php">Register a new user.</a>
                                </form>
                            </div>
                            <?php
                            if($page == "tasks"){
                                echo '<div class="tab-pane fade show active" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">';
                            } else {
                                echo '<div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">';
                            }
                            ?>
                                <h2 class="fw-bold mb-2 text-uppercase">Un-delete Tasks</h2>
                                <p class="text-white-50 mb-5">Any deleted tasks will be shown here and can be un-deleted</p>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                <div class="form-outline form-white form-floating mb-4">
                                    <select class="form-select form-control-lg" id="typeTaskX" name="task">
                                        <option disabled selected value> -- select an option -- </option>
                                        <?php
                                        $tasks = getTasks();
                                        foreach($tasks as $key => $task){
                                            echo $task["deleted"];
                                            if(!$task["deleted"]) continue;
                                            echo '<option value="' . $key . '">' . $task["title"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="page" value="tasks">
                                    <br>
                                    <button class="btn btn-outline-light btn-lg px-5" type="submit" name="submit">Un-delete</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>