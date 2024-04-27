<?php
// Initialize the session and database connection variables
$server = "localhost";
$userid = "uw05kxucdm6hu";
$pw = "n6zlygfdot3s";
$db = "dbbyejddos2r5c";
$conn = new mysqli($server, $userid, $pw, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request is coming from AJAX for user preferences
if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
    $user_id = $_POST['user_id'] ?? '';

    // Prevent SQL injection, validate and sanitize $user_id here
    
    $sql = "SELECT movie_id FROM favorite_movies WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $movie_ids = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch movie details for each movie ID
    $movie_details = [];
    foreach ($movie_ids as $movie_id) {
        $movie_details[] = fetchMovieDetails($movie_id['movie_id']);
    }

    // Return the movie details as JSON
    echo json_encode($movie_details);
    exit;
}

// Function to fetch movie details by ID
function fetchMovieDetails($movie_id) {
    $apiKey = 'd5697eb16a89b204a004af1f8fea130c';
    $movieUrl = "https://api.themoviedb.org/3/movie/$movie_id?api_key=$apiKey&language=en-US";

    $response = file_get_contents($movieUrl);
    return json_decode($response, true);
}

// Fetch movie IDs from the database
$user_id = $_SESSION["id"]; // Replace with the actual user_id value
$sql = "SELECT movie_id FROM favorite_movies WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$movie_ids = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Movies</title>
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>
    <h1>Favorite Movies</h1>
    <div id="movie-details-container"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get user ID from local storage or any other source
            var userId = 1; // Replace with the actual user ID value

            // Prepare form data for AJAX request
            var formData = new FormData();
            formData.append('user_id', userId);
            formData.append('ajax', 1); // Indicate this is an AJAX request

            // Create and send an AJAX POST request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.href, true); // POST to the same URL of this page
            xhr.onload = function () {
                if (this.status >= 200 && this.status < 300) {
                    var movieDetails = JSON.parse(this.response);
                    var container = document.getElementById('movie-details-container');

                    // Loop through fetched movie details and display them
                    movieDetails.forEach(function(movie) {
                        var movieDiv = document.createElement('div');
                        movieDiv.innerHTML = `
                            <h2>${movie.title}</h2>
                            <img src="https://image.tmdb.org/t/p/w185${movie.poster_path}" alt="${movie.title} Poster">
                            <p>${movie.overview}</p>
                            
                            <!-- Add more movie details here as needed -->
                        `;
                        container.appendChild(movieDiv);
                    });
                } else {
                    console.error(this.statusText);
                }
            };
            xhr.onerror = function () {
                console.error(this.statusText);
            };
            xhr.send(formData);
        });
    </script>
</body>
</html>
