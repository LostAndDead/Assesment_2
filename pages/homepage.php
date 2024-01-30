<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Homepage with Bootstrap</title>
    <!-- Bootstrap CSS link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Homepage</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="./login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./register.php">Register</a>
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
                    <h1>hi</h1>
                </div>
            </div>
        </div>
    </div>

<!-- Bootstrap JS and Popper.js script links (required for Bootstrap components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
