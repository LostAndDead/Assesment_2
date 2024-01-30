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

if(empty($_SESSION["uuid"])){
    header("Location: ./login.php");
}

$email = $oldPassword = $password = $passwordConfirm = "";
$msg = $oldPasswordErr = $emailErr = $passwordErr = $passwordConfirmErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["oldpassword"])) {
        $oldPasswordErr = "Password is required";
    } else {
        $oldPassword = test_input($_POST["oldpassword"]);
        if(!checkLogin($email, $oldPassword)){
            $oldPasswordErr = "Invalid Password or Email";
        }
    }

    if (empty($_POST["password"]) || empty($_POST["password_confirm"])) {
        $passwordErr = "New password is required";
    } else {
        $password = test_input($_POST["password"]);
        $passwordConfirm = test_input($_POST["password_confirm"]);
        if ($password != $passwordConfirm) {
            $passwordConfirmErr = "New passwords Dont Match";
        }
    }

    if(!$msg && !$oldPasswordErr && !$emailErr && !$passwordErr && !$passwordConfirmErr){
        if($oldPassword == $password){
            $passwordErr = "Password can not be same as old password";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $uuid = getUUID($email);
            $res = changePassword($uuid, $passwordHash);
            if($res){
                $msg = "Password changed, please login again.";
                session_destroy();
                header( "refresh:3;url=login.php?email=" . $email );
            } else {
                $msg = "Error changing password, please try again.";
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

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background: #6a11cb;
        /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
        /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
        /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        color: #fff; /* Text color for main content */
    }

    nav {
        position: fixed;
        width: 100%;
        background: rgba(169, 169, 169, 0.7); /* Gray tint for the navigation menu */
        padding: 10px;
        text-align: center;
        z-index: 1000; /* Ensures the menu is on top of other elements */
        border-radius: 0 0 15px 15px; /* Rounded corners at the bottom */
    }

    nav a {
        margin: 0 10px;
        text-decoration: none;
        color: #fff;
        transition: color 0.3s;
        border-radius: 5px; /* Rounded corners */
        padding: 8px 15px;
        background-color: #333; /* Background color for buttons */
    }

    nav a:hover {
        color: #ddd; /* Change text color on hover */
    }

    .container {
        padding: 20px;
        margin-top: 70px; /* Adjust margin to accommodate the fixed navigation menu */
    }

    .form-label {
        color: dimgray;
        font-weight: bold;
    }

    .form-label-error {
        color: red;
        font-weight: bold;
    }
</style>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="homepage.php">Home</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <?php
                    if($loggedIn){
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

<div class="container py-5 h-75">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card bg-dark text-white" style="border-radius: 1rem;">
                <div class="card-body p-3 text-center">
                    <div class="mb-md-5 mt-md-4 pb-5">
                        <h2 class="fw-bold mb-2 text-uppercase">Change Password</h2>
                        <p class="text-white-50 mb-5">Enter your details and new Password bellow.</p>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <div class="form-outline form-white form-floating mb-4">
                                <input type="email" id="typeEmailX" name="email" class="form-control form-control-lg" placeholder="name@example.com" value="<?php echo $email;?>" />
                                <?php
                                if($emailErr){
                                    echo '<label class="form-label-error" for="typeEmailX">' . $emailErr . '</label>';
                                }else {
                                    echo '<label class="form-label" for="typeEmailX">Email</label>';
                                }
                                ?>
                            </div>

                            <div class="form-outline form-white form-floating mb-4">
                                <input type="password" id="typeOldPasswordX" name="oldpassword" class="form-control form-control-lg" placeholder="password123" value="<?php echo $oldPassword;?>" />
                                <?php
                                if($oldPasswordErr){
                                    echo '<label class="form-label-error" for="typeOldPasswordX">' . $oldPasswordErr . '</label>';
                                }else {
                                    echo '<label class="form-label" for="typeEmailX">Old Password</label>';
                                }
                                ?>
                            </div>

                            <div class="form-outline form-white form-floating mb-4">
                                <input type="password" id="typePasswordX" name="password" class="form-control form-control-lg" placeholder="password123" value="<?php echo $password;?>" />
                                <?php
                                if($passwordErr){
                                    echo '<label class="form-label-error" for="typePasswordX">' . $passwordErr . '</label>';
                                }else {
                                    echo '<label class="form-label" for="typePasswordX">New Password</label>';
                                }
                                ?>
                            </div>

                            <div class="form-outline form-white form-floating mb-4">
                                <input type="password" id="typePasswordConfirmX" name="password_confirm" class="form-control form-control-lg" placeholder="password123" value="<?php echo $passwordConfirm;?>" />
                                <?php
                                if($passwordConfirmErr){
                                    echo '<label class="form-label-error" for="typePasswordConfirmX">' . $passwordConfirmErr . '</label>';
                                }else {
                                    echo '<label class="form-label" for="typePasswordConfirmX">Confirm New Password</label>';
                                }
                                ?>
                            </div>

                            <?php
                            if($msg){
                                echo '<div>';
                                echo '<p class="mb-0">' . $msg . '</p>';
                                echo '<div>';
                            }
                            ?>
                            <button class="btn btn-outline-light btn-lg px-5" type="submit" name="submit">Change</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>