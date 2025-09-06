<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Login</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php
    require('db.php');
    session_start();

    // When form submitted, check and create user session.
    if (isset($_POST['studentNo']) && isset($_POST['password'])) {
        $studentNo = mysqli_real_escape_string($con, $_POST['studentNo']);
        $password  = $_POST['password'];

        // Check if user exists
        $query  = "SELECT * FROM users WHERE student_no='$studentNo' LIMIT 1";
        $result = mysqli_query($con, $query) or die("Query failed: " . mysqli_error($con));

        if ($row = mysqli_fetch_assoc($result)) {
            // Verify password (hashed in registration.php)
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['studentNo'] = $studentNo;
                header("Location: home.php");
                exit();
            } else {
                echo "<script>alert('Invalid password. Try again.');</script>";
            }
        } else {
            echo "<script>alert('Student not found. Try again or register.');</script>";
        }
    }
?>
    <form class="form" method="post" name="login">
        <h1 class="login-title">LOGIN</h1>
        <input type="text" class="login-input" name="studentNo" placeholder="Student number" required autofocus/>
        <input type="password" class="login-input" name="password" placeholder="Password" required/>
        <input type="submit" value="Login" name="submit" class="login-button"/>
        <p class="link"><a href="registration.php">New Registration</a></p>
    </form>
</body>
</html>