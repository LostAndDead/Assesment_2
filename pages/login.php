<?php
require "../utils/sql.php";

$email = $username = $password = $passwordConfirm = "";
$msg = $usernameErr = $emailErr = $passwordErr = "";

session_start();

if(!empty($_SESSION["uuid"])){
    header("Location: ./homepage.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = test_input($_POST["password"]);
    }

    if(!$emailErr && !$passwordErr){
        $result = checkLogin($email, $password);
        $user = getUser(getUUID($email));
        if($result){
            if(!$user["active"]) {
                $msg = "User is disabled, please contact an admin.";
            } else {
                logAction(getUUID($email), EventType::LOGGED_IN, $_SERVER['REMOTE_ADDR']);
                $_SESSION["uuid"] = getUUID($email);
                $_SESSION["session_uuid"] = password_hash(generateSession(getUUID($email)), PASSWORD_DEFAULT);
                $msg = "Login successful, redirecting...";
                header( "refresh:3;url=homepage.php");
            }

        } else {
            $passwordErr = "Invalid Email or Password";
            $emailErr = "Invalid Email or Password";
        }
    }
}

if($_SERVER["REQUEST_METHOD"] == "GET") {
    if(!empty($_GET["email"])){
        $email = $_GET["email"];
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
    <title>Login</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="homepage.php">Home</a>
    </div>
</nav>

<div class="container py-5 h-75">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card bg-dark text-white" style="border-radius: 1rem;">
                <div class="card-body p-3 text-center">
                    <div class="mb-md-5 mt-md-4 pb-5">
                        <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                        <p class="text-white-50 mb-5">Please enter your login and password!</p>
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
                                <input type="password" id="typePasswordX" name="password" class="form-control form-control-lg" placeholder="password123" value="<?php echo $password;?>" />
                                <?php
                                if($passwordErr){
                                    echo '<label class="form-label-error" for="typePasswordX">' . $passwordErr . '</label>';
                                }else {
                                    echo '<label class="form-label" for="typePasswordX">Password</label>';
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
                            <button class="btn btn-outline-light btn-lg px-5" type="submit" name="submit">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>