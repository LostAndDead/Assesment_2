<?php

require "../utils/sql.php";

$email = $username = $password = $passwordConfirm = "";
$msg = $usernameErr = $emailErr = $passwordErr = $passwordConfirmErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/",$username)) {
            $usernameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["password"]) || empty($_POST["password_confirm"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
        $passwordConfirm = test_input($_POST["password_confirm"]);
        if ($password != $passwordConfirm) {
            $passwordConfirmErr = "Passwords Dont Match";
        }
    }

    if(!$msg && !$usernameErr && !$emailErr && !$passwordErr && !$passwordConfirmErr){
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $result = createUser($username, $email, $passwordHash, false);
        switch ($result){
            case "Duplicate": {
                $msg = "Email or username is already in use";
                break;
            }
            case "Worked": {
                $msg = "User created, you will be redirected to login.";
                header( "refresh:3;url=login.php?email=" . $email );
                break;
            }
            case "shrug": {
                $msg = "Something went wrong, I dont know how. This should never happen";
                break;
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
    .gradient-custom {
        /* fallback for old browsers */
        background: #6a11cb;

        /* Chrome 10-25, Safari 5.1-6 */
        background: -webkit-linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));

        /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1))
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

<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white" style="border-radius: 1rem;">
                    <div class="card-body p-3 text-center">

                        <div class="mb-md-5 mt-md-4 pb-5">

                            <h2 class="fw-bold mb-2 text-uppercase">Register</h2>
                            <p class="text-white-50 mb-5">Please enter your details to register.</p>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                <div class="form-outline form-white form-floating mb-4">
                                    <input type="text" id="typeUsernameX" name="username" class="form-control form-control-lg" placeholder="username" value="<?php echo $username;?>" />
                                    <?php
                                    if($usernameErr){
                                        echo '<label class="form-label-error" for="typeUsernameX">' . $usernameErr . '</label>';
                                    }else {
                                        echo '<label class="form-label" for="typeUsernameX">Username</label>';
                                    }
                                    ?>
                                </div>

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
                                    <input type="password" id="typePasswordX" name="password" class="form-control form-control-lg" placeholder="password123" value="<?php echo $password;?>" />
                                    <?php
                                    if($passwordErr){
                                        echo '<label class="form-label-error" for="typePasswordX">' . $passwordErr . '</label>';
                                    }else {
                                        echo '<label class="form-label" for="typePasswordX">Password</label>';
                                    }
                                    ?>
                                </div>

                                <div class="form-outline form-white form-floating mb-4">
                                    <input type="password" id="typePasswordConfirmX" name="password_confirm" class="form-control form-control-lg" placeholder="password123" value="<?php echo $passwordConfirm;?>" />
                                    <?php
                                    if($passwordConfirmErr){
                                        echo '<label class="form-label-error" for="typePasswordConfirmX">' . $passwordConfirmErr . '</label>';
                                    }else {
                                        echo '<label class="form-label" for="typePasswordConfirmX">Password Confirmation</label>';
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



                                <button class="btn btn-outline-light btn-lg px-5" type="submit" name="submit">Register</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!--

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    Username: <input type="text" name="username" value="<?php echo $username;?>">
    <span class="error"><?php echo $usernameErr;?></span>
    <br><br>
    E-mail: <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error"><?php echo $emailErr;?></span>
    <br><br>
    Password: <input type="text" name="password" value="<?php echo $password;?>">
    <span class="error"><?php echo $passwordErr;?></span>
    <br><br>
    Password Confirm: <input type="text" name="password_confirm" value="<?php echo $passwordConfirm;?>">
    <br><br>
    <input type="submit" name="submit" value="Submit">
    <br>
    <span class="error"><?php echo $msg;?></span>
</form>
-->
