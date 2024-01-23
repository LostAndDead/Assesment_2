<?php

require "../utils/sql.php";

$email = $username = $password = $passwordConfirm = "";
$err = $usernameErr = $emailErr = $passwordErr = "";

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
            $passwordErr = "Passwords Dont Match";
        }
    }

    if(!$err && !$usernameErr && !$emailErr && !$passwordErr){
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $result = createUser($username, $email, $passwordHash, false);
        switch ($result){
            case "Duplicate": {
                $err = "Email or username is already in use";
                break;
            }
            case "Worked": {
                $err = "User created, you will be redirected to login.";
                header( "refresh:3;url=login.php?email=" . $email );
                break;
            }
            case "shrug": {
                $err = "Something went wrong, I dont know how. This should never happen";
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
<body>

<h1> Register Account </h1>

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
    <span class="error"><?php echo $err;?></span>
</form>
