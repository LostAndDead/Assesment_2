<?php
    session_start();

    session_destroy();

    header( "refresh:2;url=homepage.php");
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5 h-75">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card bg-dark text-white" style="border-radius: 1rem;">
                <div class="card-body p-3 text-center">
                    <div class="mb-md-1 mt-md-1 pb-1">
                        <h2 class="fw-bold mb-1 text-uppercase">Logging out...</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
