<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <div class="container">
        <?php
        $server = "localhost";
        $userid = "u0kg2ws5z36zq";
        $pw = "rzuoxy5bnggz";
        $db = "dbioookmqfj5gb";
        $conn = new mysqli($server, $userid, $pw, $db);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        session_start();
        $email_err = $password_err = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];
            $password = $_POST['password'];

            if (empty($email)) {
                $email_err = "Please enter your email.";
            }

            if (empty($password)) {
                $password_err = "Please enter your password.";
            }

            if (empty($email_err) && empty($password_err)) {
                $sql = "SELECT id, email, password FROM users WHERE email = ?";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $param_email);
                    $param_email = $email;

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_store_result($stmt);

                        if (mysqli_stmt_num_rows($stmt) == 1) {
                            mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password);
                            if (mysqli_stmt_fetch($stmt)) {
                                if (password_verify($password, $hashed_password)) {
                                    session_start();
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["id"] = $id;
                                    $_SESSION["email"] = $email;

                                    header("location: welcome.php");
                                } else {
                                    $password_err = "The password you entered was not correct.";
                                }
                            }
                        } else {
                            $email_err = "No account found with that email.";
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    mysqli_stmt_close($stmt);
                }
            }
            mysqli_close($conn);
        }
        ?>
        <h2>Login to Your Account</h2>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <span class="error"><?php echo $email_err; ?></span>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <span class="error"><?php echo $password_err; ?></span>

            <button type="submit">Login</button>
            <p class="alternate-action">Don't have an account? <a href="signup.php">Sign Up</a></p>
        </form>
    </div>
</body>
</html>
