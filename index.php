<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>

    <title>Login Rental Mobil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body class="d-flex justify-content-center align-items-center">

<div class="glass login-box">

    <div class="login-title">
        🚗 Rental Mobil
    </div>

    <form action="proses_login.php" method="POST">

        <div class="mb-3">

            <input
                type="text"
                name="username"
                class="form-control"
                placeholder="Username"
                required>

        </div>

        <div class="mb-4">

            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Password"
                required>

        </div>

        <button class="btn-modern">
            Login
        </button>

    </form>

</div>

</body>
</html>