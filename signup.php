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
$email_err = $password_err = $signup_err = "";

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
        // Prepare a select statement to check if the email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already taken.";
                } else{
                    // Prepare an insert statement
                    $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
                     
                    if($stmt = mysqli_prepare($conn, $sql)){
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "ss", $param_email, $param_password);
                        
                        // Set parameters
                        $param_email = $email;
                        $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                        
                        // Attempt to execute the prepared statement
                        if(mysqli_stmt_execute($stmt)){
                            // Retrieve the user ID of the newly inserted user
                            $user_id = mysqli_insert_id($conn);

                            // Close statement
                            mysqli_stmt_close($stmt);

                            // Store user ID in local storage
                            echo "<script>localStorage.setItem('userID', $user_id);</script>";

                            // Redirect user to index.html
                            header("location: preferences.php");
                            exit;
                        } else{
                            $signup_err = "Oops! Something went wrong. Please try again later.";
                        }
                    }
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
<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="signup.css">
    <title>Sign Up</title>
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <span class="error"><?php echo $email_err; ?></span>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <span class="error"><?php echo $password_err; ?></span>

            <button type="submit">Sign Up</button>
            <p class="alternate-action">Already have an account? <a href="login.php">Login</a></p>
            <span class="error"><?php echo $signup_err; ?></span>
        </form>
    </div>
</body>
</html> -->
<!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://kit.fontawesome.com/46b7ceee20.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="css/signup.css">

        <title>Sign Up</title>
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
                <h2>Come join us!</h2>
                <p>We are so excited to have you here. If you haven't already, create an account to get access to our services and offers.</p>
                <p class="alternate-action">Already have an account? </p>
                <p> <a href="login.php" class="login-link">Log in here</a></p>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="container">
            <h2>Sign Up</h2>
            <form method="POST">
                <label for="email">Enter Your Email</label>
                <input type="email" id="email" name="email" required>
                <span class="error"><?php echo $email_err; ?></span>

                <label for="password">Create a Password</label>
                <input type="password" id="password" name="password" required>
                <span class="error"><?php echo $password_err; ?></span>

                <button type="submit">Sign Up</button>
                <span class="error"><?php echo $signup_err; ?></span>
            </form>
        </div>
    </div>
</div>


    </body>

    </html>
