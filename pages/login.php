<?php
require "../utils/sql.php";

$email = $username = $password = $passwordConfirm = "";
$msg = $usernameErr = $emailErr = $passwordErr = "";

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
        if($result){
            $msg = "Login successful";
            header("Location: ./homepage.php?login=true");
        } else {
            $msg = "Invalid Email or Password";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>

<h1> Login Please </h1>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    E-mail: <input type="text" name="email" value="<?php echo $email;?>">
    <span class="error">* <?php echo $emailErr;?></span>
    <br><br>
    Password: <input type="text" name="password" value="<?php echo $password;?>">
    <span class="error"><?php echo $passwordErr;?></span>
    <br><br>
    <input type="submit" name="submit" value="Submit">
    <br>
    <span class="error"><?php echo $msg;?></span>
</form>