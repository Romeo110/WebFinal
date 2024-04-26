<?php
// Start a session
session_start();

$server = "localhost";
$userid = "uw05kxucdm6hu";
$pw = "n6zlygfdot3s";
$db = "dbbyejddos2r5c";
$conn = new mysqli($server, $userid, $pw, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, email, password FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if email exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;                            
                            
                            // Redirect user to index.html
                            header("location: preferences.php");
                            exit;
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else{
                    // Email doesn't exist, display a generic error message
                    $login_err = "Invalid email or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/46b7ceee20.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/signup_login.css">
    <title>Login</title>
</head>
<body>
        <!-- nav bar with only home button -->
        <div class="navbar">
            <div class="horizontal-navbar">
                <ul>
                    <li>
                        <a href="index.html" class="nav-link">
                            <span class="item-icon">
                            <i class='bx bxs-home'></i>
                        </span>
                            <span class="item-txt">
                            Home
                        </span>
                        </a>
                    </li>
                    <!-- Additional menu items if needed -->
                </ul>
            </div>
        </div>
        <div class="signup-card">
    <div class="intro-section">
        <div class="signup-intro">
            <div class="intro-control__inner">
                <h2>Welcome back!</h2>
                <p>We are so excited to have you back with us. Pick out the newest and hottest shows and movies to watch today.</p>
                <p class="alternate-action">Don't have an account? </p>
                <p> <a href="signup.php" class="login-link">Sign up here</a></p>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="container">
            <h2>Log In</h2>
            <form method="POST">
                <label for="email">Enter Your Email</label>
                <input type="email" id="email" name="email" required>
                <span class="error"><?php echo $email_err; ?></span>

                <label for="password">Enter Your Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error"><?php echo $password_err; ?></span>

                <button type="submit">Login</button>
                <span class="error"><?php echo $login_err; ?></span>
            </form>
        </div>
    </div>
</div>

    <script>
        // Store user ID in local storage after successful login
        <?php if(isset($_SESSION['id'])) : ?>
            localStorage.setItem('userID', <?php echo $_SESSION['id']; ?>);
        <?php endif; ?>
    </script>
</body>
</html>

