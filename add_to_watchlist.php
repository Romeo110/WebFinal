<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$server = "localhost";
$userid = "uw05kxucdm6hu";
$pw = "n6zlygfdot3s";
$db = "dboyek8cty39tn";
$conn = new mysqli($server, $userid, $pw, $db);

if ($conn->connect_error) {
    // Log connection error
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve user ID
    $user_id = $_SESSION["id"];

    // Retrieve movie ID from the request body
    $data = json_decode(file_get_contents("php://input"), true);
    $movie_id = $data["movieId"];

    // Check if the movie is already in the watchlist
    $query = "SELECT 1 FROM favorite_movies WHERE user_id = ? AND movie_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // If already in the watchlist, return success
        exit(json_encode(["success" => true, "alreadyInWatchlist" => true]));
    }

    // Insert the movie into the watchlist
    $insertQuery = "INSERT INTO favorite_movies (user_id, movie_id) VALUES (?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param('ii', $user_id, $movie_id);
    if ($insertStmt->execute()) {
        exit(json_encode(["success" => true]));
    } else {
        // If unable to execute the insert, return an error message
        error_log("Error adding movie to watchlist: " . $conn->error);
        http_response_code(500);
        exit(json_encode(["success" => false, "message" => "Error adding movie to watchlist"]));
    }
}

// Fetch movie details and display HTML
// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>