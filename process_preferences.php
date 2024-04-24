<?php
// Start a session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Redirect the user to the login page if not logged in
    header("location: login.php");
    exit;
}

$server = "localhost";
$userid = "uw05kxucdm6hu";
$pw = "n6zlygfdot3s";
$db = "dbbyejddos2r5c";
$conn = new mysqli($server, $userid, $pw, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if preferences are submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user ID from session
    $user_id = $_SESSION["id"];

    // Retrieve preferences from POST data
    $genre = implode(', ', $_POST['genres'] ?? []);
    $actor_director = json_encode($_POST['selected_actors_directors'] ?? []);
    $min_decade = $_POST['min_decade'] ?? "";
    $max_decade = $_POST['max_decade'] ?? "";
    $language = json_encode($_POST['selected_languages'] ?? []);

    // Prepare and bind SQL statement
    $sql = "INSERT INTO preferences (user_id, genre, actor_director, min_decade, max_decade, language) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $user_id, $genre, $actor_director, $min_decade, $max_decade, $language);

    // Execute the prepared statement
    $stmt->execute();

    // Close the statement
    $stmt->close();
} else {
    // Redirect the user to an appropriate page if preferences are not submitted
    header("location: error.php");
    exit;
}

$conn->close();
header("location: index.html");
?>
