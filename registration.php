<?php
require('db.php');
session_start();

$errors = [];
$success = false;
$first_name = $last_name = $student_no = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $student_no = trim($_POST['student_no'] ?? '');
    $password   = $_POST['password'] ?? '';

    if ($first_name === '') $errors[] = 'First name is required.';
    if ($last_name === '')  $errors[] = 'Last name is required.';
    if ($student_no === '') $errors[] = 'Student number is required.';
    if ($password === '')   $errors[] = 'Password is required.';

    if (empty($errors)) {
        $checkSql = "SELECT id FROM users WHERE student_no = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $checkSql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $student_no);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $exists = mysqli_stmt_num_rows($stmt) > 0;
            mysqli_stmt_close($stmt);

            if ($exists) {
                $errors[] = "A user with student number '{$student_no}' already exists.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insertSql = "INSERT INTO users (student_no, first_name, last_name, password_hash) VALUES (?, ?, ?, ?)";
                $ins = mysqli_prepare($con, $insertSql);
                if ($ins) {
                    mysqli_stmt_bind_param($ins, 'ssss', $student_no, $first_name, $last_name, $hash);
                    $ok = mysqli_stmt_execute($ins);
                    if ($ok) {
                        $success = true;
                    } else {
                        $errors[] = "Database error: " . mysqli_error($con);
                    }
                    mysqli_stmt_close($ins);
                } else {
                    $errors[] = "Database prepare error: " . mysqli_error($con);
                }
            }
        } else {
            $errors[] = "Database error: " . mysqli_error($con);
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Registration</title>
<link rel="stylesheet" href="style.css"/>
<style>
/* fallback styles to ensure button visible */
.login-button, .action-button {
  display:inline-block; padding:10px 16px; border:none; background:#2b7cff; color:#fff;
  text-decoration:none; border-radius:4px; cursor:pointer; font-size:14px;
}
.formRegister { max-width:420px; margin:30px auto; padding:18px; border:1px solid #ddd; border-radius:6px; }
.formRegister input { width:100%; padding:10px; margin:8px 0; box-sizing:border-box; }
.message { margin:12px 0; padding:10px; border-radius:4px; }
.error { background:#ffe6e6; border:1px solid #ffb3b3; }
.success { background:#e6ffea; border:1px solid #b3ffcc; }
</style>
</head>
<body>

<div class="formRegister">
    <h1 class="login-title">REGISTER</h1>

    <?php
    if (!empty($errors)) {
        echo '<div class="message error"><strong>Please fix the errors below:</strong><ul>';
        foreach ($errors as $e) {
            echo '<li>' . htmlspecialchars($e) . '</li>';
        }
        echo '</ul></div>';
    }

    if ($success) {
        echo '<div class="message success"><strong>Registration successful!</strong><br>';
        echo 'You can now sign in with your student number and password.';
        echo '<p style="margin-top:10px;"><a class="action-button" href="index.php">Go to Login</a></p></div>';
    } else {
    ?>
        <form action="registration.php" method="post" autocomplete="off">
            <input type="text" name="first_name" placeholder="First Name" required
                   value="<?php echo htmlspecialchars($first_name); ?>">
            <input type="text" name="last_name" placeholder="Last Name" required
                   value="<?php echo htmlspecialchars($last_name); ?>">
            <input type="text" name="student_no" placeholder="Student Number" required
                   value="<?php echo htmlspecialchars($student_no); ?>">
                   <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="submit" class="login-button">SIGN UP</button>
            <p style="margin-top:12px;">Already registered? <a href="index.php">Click to Login</a></p>
        </form>
    <?php
    } // end else $success
    ?>

</div>

</body>
</html>