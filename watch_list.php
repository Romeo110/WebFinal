<?php
// Start the session
session_start();

// Set error reporting to zero to prevent unwanted output
error_reporting(0);

// Initialize database connection variables
$server = "localhost";
$userid = "uw05kxucdm6hu";
$pw = "n6zlygfdot3s";
$db = "dboyek8cty39tn";
$conn = new mysqli($server, $userid, $pw, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch movie details by ID from the external API
function fetchMovieDetails($movie_id) {
    $apiKey = 'd5697eb16a89b204a004af1f8fea130c';
    $movieUrl = "https://api.themoviedb.org/3/movie/$movie_id?api_key=$apiKey&language=en-US";
    $response = @file_get_contents($movieUrl);
    if ($response === FALSE) {
        return ["error" => "Failed to fetch data from API."];
    }
    return json_decode($response, true);
}

// Check if the request is coming from AJAX for user preferences
if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
    $user_id = $_SESSION["id"] ?? null;
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
        $details = fetchMovieDetails($movie_id['movie_id']);
        if (isset($details['error'])) {
            $movie_details[] = $details;
        } else {
            $movie_details[] = $details;
        }
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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/46b7ceee20.js" crossorigin="anonymous"></script>
    <title>Your Watch List</title>
    <link rel="stylesheet" href="css/recommend.css">
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
                <li>
                    <a href="movies.html" class="nav-link">
                        <span class="item-icon">
                            <i class='bx bx-movie'></i>
                        </span>
                        <span class="item-txt">
                            Movies
                        </span>
                    </a>
                </li>
                <li>
                    <a href="recommend.php" class="nav-link">
                        <span class="item-icon">
                            <i class='bx bxs-dish'></i>
                        </span>
                        <span class="item-txt">
                            MadeForYou
                        </span>
                    </a>
                </li>
                <li>
                    <a href="watch_list.php" class="nav-link">
                        <span class="item-icon">
                            <i class='bx bxs-user'></i>
                        </span>
                        <span class="item-txt">
                            WatchList
                        </span>
                    </a>
                </li>
                <li>
                    <a href="preferences.php" class="nav-link">
                        <span class="item-icon">
                            <i class='bx bx-cog'></i>
                        </span>
                        <span class="item-txt">
                            Preferences
                        </span>
                    </a>
                </li>
                <li>
                    <a href="compare.html" class="nav-link">
                        <span class="item-icon">
                          <i class='bx bxl-deezer'></i>
                  </span>
                        <span class="item-txt">
                      CompareTool
                  </span>
                    </a>
                </li>
                <!-- Additional menu items if needed -->
            </ul>
        </div>
    </div>

    <div class="main-content">
    <section id="recommendations">
    <h2>WatchList</h2>
    </div>

    <!-- Footer -->
    <footer class="footer">
            <div class="background">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="100%" height="100%" viewBox="0 0 1600 900">
          <defs>
              <linearGradient id="bg" x2="0%" y2="100%">
                  <stop
                      offset="0%"
                      style="stop-color: #642b5b"
                  ></stop>
                  <stop
                      offset="100%"
                      style="stop-color: #fdf6fc"
                  ></stop>
              </linearGradient>
              <path
                  id="wave"
                  fill="url(#bg)"
                  d="M-363.852,502.589c0,0,236.988-41.997,505.475,0
      s371.981,38.998,575.971,0s293.985-39.278,505.474,5.859s493.475,48.368,716.963-4.995v560.106H-363.852V502.589z"
              />
          </defs>
          <g>
              <use xlink:href="#wave" opacity=".3">
                  <animateTransform
                      attributeName="transform"
                      attributeType="XML"
                      type="translate"
                      dur="8s"
                      calcMode="spline"
                      values="270 230; -334 180; 270 230"
                      keyTimes="0; .5; 1"
                      keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
                      repeatCount="indefinite"
                  />
              </use>
              <use xlink:href="#wave" opacity=".6">
                  <animateTransform
                      attributeName="transform"
                      attributeType="XML"
                      type="translate"
                      dur="6s"
                      calcMode="spline"
                      values="-270 230;243 220;-270 230"
                      keyTimes="0; .6; 1"
                      keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
                      repeatCount="indefinite"
                  />
              </use>
              <use xlink:href="#wave" opacty=".9">
                  <animateTransform
                      attributeName="transform"
                      attributeType="XML"
                      type="translate"
                      dur="4s"
                      calcMode="spline"
                      values="0 230;-140 200;0 230"
                      keyTimes="0; .4; 1"
                      keySplines="0.42, 0, 0.58, 1.0;0.42, 0, 0.58, 1.0"
                      repeatCount="indefinite"
                  />
              </use>
          </g>
          <text x="50%" y="93%" dominant-baseline="middle" text-anchor="middle" fill="white" font-size="15">
              &copy; 2024 FilmFinder. All rights reserved.
          </text>
      </svg>
            </div>

        </footer>

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
                        if (movieDetails.error) {
                            console.error("Error from server:", movieDetails.error);
                            return;
                        }
                        var container = document.getElementById('main-content');
                        movieDetails.forEach(function(movie) {
                            if (movie.error) {
                                console.error("Error loading movie details:", movie.error);
                                return;
                            }
                            var movieDiv = document.createElement('div');
                            movieDiv.innerHTML = `
                                <h2>${movie.title}</h3>
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

                    // footer swap
                    window.addEventListener('scroll', function() {
                var scrollPosition = window.scrollY;
                var windowHeight = window.innerHeight;
                var documentHeight = document.documentElement.scrollHeight;
                var threshold = 30;

                // Check if the scrollbar is at the bottom of the page
                if (scrollPosition + windowHeight >= documentHeight - threshold) {
                    // Show animated footer
                    document.body.classList.add('show-footer');
                } else {
                    // Hide animated footer
                    document.body.classList.remove('show-footer');
                }
            });
    </script>
</body>
</html>



