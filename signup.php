<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sign Up</title>
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

        $email_err = $password_err = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];
            $password = $_POST['password'];

            if (empty($email)) {
                $email_err = "Please enter email.";
            }

            if (empty($password)) {
                $password_err = "Please enter a password.";
            } else {
                $password = password_hash($password, PASSWORD_DEFAULT);
            }

            if (empty($email_err) && empty($password_err)) {
                $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ss", $email, $password);

                    if (mysqli_stmt_execute($stmt)) {
                        header("location: login.php"); // Redirect to login page after successful registration
                    } else {
                        echo "Something went wrong. Please try again later.";
                    }

                    mysqli_stmt_close($stmt);
                }
            }
            mysqli_close($conn);
        }
        ?>
         <h2>Create Your Account</h2>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <span class="error"><?php echo $email_err; ?></span>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <span class="error"><?php echo $password_err; ?></span>

            <button type="submit">Sign Up</button>
            <p class="alternate-action">Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
</body>
</html>
