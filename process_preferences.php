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

    // Check if the user ID already exists in the preferences table
    $sql_check = "SELECT id FROM preferences WHERE user_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // User ID exists, so update the existing record with new preferences
        $sql_update = "UPDATE preferences 
                       SET genre = ?, actor_director = ?, min_decade = ?, max_decade = ?, language = ? 
                       WHERE user_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssi", $genre, $actor_director, $min_decade, $max_decade, $language, $user_id);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // User ID does not exist, so insert a new record with the preferences
        $sql_insert = "INSERT INTO preferences (user_id, genre, actor_director, min_decade, max_decade, language) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isssss", $user_id, $genre, $actor_director, $min_decade, $max_decade, $language);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
} else {
    // Redirect the user to an appropriate page if preferences are not submitted
    header("location: error.php");
    exit;
}

// Close the database connection
$conn->close();
header("location: index.html");
?>
