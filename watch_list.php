<?php
// Start the session
session_start();

// Set error reporting to zero to prevent unwanted output
error_reporting(0);

// Initialize database connection variables
$server = "localhost";
$userid = "uw05kxucdm6hu";
$pw = "n6zlygfdot3s";
$db = "dbbyejddos2r5c";
$conn = new mysqli($server, $userid, $pw, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch movie details by ID from the external API
function fetchMovieDetails($movie_id) {
    $apiKey = 'd5697eb16a89b204a004af1f8fea130c';
    $movieUrl = "https://api.themoviedb.org/3/movie/$movie_id?api_key=$apiKey&language=en-US";
    $response = file_get_contents($movieUrl);
    return json_decode($response, true);
}

// Check if the request is coming from AJAX for user preferences
if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
    $user_id = $_SESSION["user_id"] ?? null;
    if ($user_id === null) {
        echo json_encode(["error" => "User ID not set in session."]);
        exit;
    }

    $sql = "SELECT movie_id FROM favorite_movies WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        echo json_encode(["error" => "Database error: " . $conn->error]);
        exit;
    }

    $movie_ids = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $movie_details = [];
    foreach ($movie_ids as $movie_id) {
        $movie_details[] = fetchMovieDetails($movie_id['movie_id']);
    }

    header('Content-Type: application/json');
    echo json_encode($movie_details);
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Movies</title>
</head>
<body>
    <h1>Favorite Movies</h1>
    <div id="movie-details-container"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var formData = new FormData();
            formData.append('ajax', 1);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.href, true);
            xhr.onload = function () {
                if (this.status >= 200 && this.status < 300) {
                    try {
                        var movieDetails = JSON.parse(this.response);
                        var container = document.getElementById('movie-details-container');
                        movieDetails.forEach(function(movie) {
                            var movieDiv = document.createElement('div');
                            movieDiv.innerHTML = `
                                <h2>${movie.title}</h2>
                                <img src="https://image.tmdb.org/t/p/w185${movie.poster_path}" alt="${movie.title} Poster">
                                <p>${movie.overview}</p>
                            `;
                            container.appendChild(movieDiv);
                        });
                    } catch (e) {
                        console.error("Error parsing JSON:", e);
                    }
                } else {
                    console.error("Server responded with status:", this.status);
                }
            };
            xhr.onerror = function () {
                console.error("Request failed:", this.statusText);
            };
            xhr.send(formData);
        });
    </script>
</body>
</html>

